<?php

namespace App\Http\Controllers\Admin\Accessory;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessoryConsumeRequest;
use App\Models\Accessory;
use App\Models\AccessoryConsume;
use App\Models\AccessoryItem;
use App\Models\AccessoryItemUse;
use App\Services\CodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessoryConsumeController extends Controller
{
    protected $type = "Consume";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list accessory-consume');
        $sql = AccessoryConsume::orderBy('id', 'DESC')->with(['items']);

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('date', 'LIKE', '%'. $request->q.'%')
                ->orWhere('note', 'LIKE', '%'. $request->q.'%')
                ->orWhere('total_quantity', 'LIKE', '%'. $request->q.'%')
                ->orWhere('total_amount', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('accessory', function($q) use($request) {
                $q->where('name', $request->q);
            });
        }

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.accessory.consume', compact('result'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add accessory-consume');
        $items = [
            (object)[
                'id' => null,
                'accessory_id' => null,
                'quantity' => null,
                'unit_price' => null,
                'total_amount' => null,
            ]
        ];

        $accessories = Accessory::where('status','Active')->get();
        return view('admin.accessory.consume', compact('items', 'accessories'))->with('create', 1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   
    public function store(AccessoryConsumeRequest $request)
    {
        $this->authorize('add accessory-consume');

        $code = CodeService::generate(AccessoryConsume::class, 'AC', 'code');
        try {
            DB::beginTransaction();


            $data = AccessoryConsume::create([
                'code' => $code,
                'date' => dbDateFormat($request->date),
                'note' => $request->note,
                'total_quantity' => $request->total_quantity ?? 0,
                'total_amount' => $request->subtotal_amount ?? 0,
                'created_by' => auth()->user()->id
            ]);

            if ($request->only('accessory_id')) {
                foreach ($request->accessory_id as $key => $row) {
                    $accessory_id = $request->accessory_id[$key];
                    $stockIn = AccessoryItem::where('accessory_id', $accessory_id)->where('status', 0)->where('type','Purchase')->sum('quantity');
                    $stockOut = AccessoryItem::where('accessory_id', $accessory_id)->where('status', 0)->where('type','Purchase')->sum('used_quantity');
                    $stock = $stockIn - $stockOut;
                    if( $request->quantity[$key] > 0 && $stock >= $request->quantity[$key]){
                     $accessoryOut =  AccessoryItem::create([
                            'type' => $this->type,
                            'flagable_id' => $data->id,
                            'flagable_type' => AccessoryConsume::class,
                            'accessory_id' => $request->accessory_id[$key],
                            'quantity' => $request->quantity[$key],
                            'unit_price' => $request->unit_price[$key],
                            'total_amount' => $request->amount[$key],
                            'created_at' => now(),
                        ]);
                        $stockList = AccessoryItem::where('accessory_id', $accessory_id)->where('type','Purchase')->where('status', 0)->orderBy('id', 'asc')->get();
                        $soldQuantity = 0;
                        foreach ($stockList as $value) {
                            $remainingQuantity = $request->quantity[$key] - $soldQuantity;
                            $availableQuantity = $value->quantity - $value->used_quantity;
                            if ($remainingQuantity >= $availableQuantity) {
                                AccessoryItemUse::create([
                                    'accessory_out_id' => $accessoryOut->id,
                                    'accessory_in_id' => $value->id,
                                    'quantity' => $availableQuantity
                                ]);
                                AccessoryItem::where('id', $value->id)->update([
                                    'status' => 1,
                                    'used_quantity' => $value->used_quantity + $availableQuantity
                                ]);
                                $soldQuantity += $availableQuantity;
                            } else {
                                AccessoryItemUse::create([
                                    'accessory_out_id' => $accessoryOut->id,
                                    'accessory_in_id' => $value->id,
                                    'quantity' => $remainingQuantity
                                ]);
                                AccessoryItem::where('id', $value->id)->update([
                                    'status' => 0,
                                    'used_quantity' => $value->used_quantity + $remainingQuantity
                                ]);
                                $soldQuantity += $remainingQuantity;
                                break;
                            }
                        }
                        if ($soldQuantity < $request->quantity[$key]) {
                            DB::rollBack();
                            $request->session()->flash('errorMessage', 'Insufficient stock');
                            return false;
                        }
                    } else{
                        DB::rollBack();
                        $request->session()->flash('errorMessage', 'Insufficient stock');
                        return false;
                    }
    
                }
            }
            DB::commit();
            $request->session()->flash('successMessage', 'Accessory Consume was successfully added!');
            return redirect()->route('admin.accessory.consume.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.accessory.consume.index');
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $this->authorize('show accessory-consume');
        $data = AccessoryConsume::with('items')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.consume.index', qArray());
        }

        return view('admin.accessory.consume', compact('data'))->with('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->authorize('edit accessory-consume');
        $data = AccessoryConsume::with('items')->find($id);
        $items = $data->items;
        $accessories = Accessory::where('status','Active')->get();
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.consume.index', qArray());
        }
        return view('admin.accessory.consume', compact('data','accessories','items'))->with('edit', $id);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccessoryConsumeRequest $request, $id)
    {
        $this->authorize('edit accessory-consume');
        $data = AccessoryConsume::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Accessory Purchase not found!');
            return redirect()->route('admin.accessory.consume.index', qArray());
        }

        try {
            DB::beginTransaction();

            $data->update([
                'date' => dbDateFormat($request->date),
                'note' => $request->note,
                'total_quantity' => $request->total_quantity ?? 0,
                'total_amount' => $request->subtotal_amount ?? 0,
                'updated_by' => auth()->user()->id
            ]);
        if ($request->only('accessory_id')) {
            // Return and Delete Assigned Value
            $fgOutItem = AccessoryItem::where('flagable_id', $id)->where('flagable_type', AccessoryConsume::class)->where('type', 'Consume')->pluck('id');
            $fgUsage = AccessoryItemUse::whereIn('accessory_out_id', $fgOutItem)->get();
            foreach($fgUsage as $fgUsageVal){
                $fgGrnItem = AccessoryItem::where('id', $fgUsageVal->accessory_in_id)->first();
                $fgGrnItem->update([
                    'status' => 0,
                    'used_quantity' => $fgGrnItem->used_quantity - $fgUsageVal->quantity,
                ]);
            }
            AccessoryItemUse::whereIn('accessory_out_id', $fgOutItem)->delete();
            AccessoryItem::where('flagable_id', $id)->where('flagable_type', AccessoryConsume::class)->where('type', 'Consume')->delete();

            foreach ($request->accessory_id as $key => $row) {
                $accessory_id = $request->accessory_id[$key];
                $stockIn = AccessoryItem::where('accessory_id', $accessory_id)->where('status', 0)->where('type','Purchase')->sum('quantity');
                $stockOut = AccessoryItem::where('accessory_id', $accessory_id)->where('status', 0)->where('type','Purchase')->sum('used_quantity');
                $stock = $stockIn - $stockOut;
                if( $request->quantity[$key] > 0 && $stock >= $request->quantity[$key]){
                 $accessoryOut =   AccessoryItem::create([
                        'type' => $this->type,
                        'flagable_id' => $data->id,
                        'flagable_type' => AccessoryConsume::class,
                        'accessory_id' => $request->accessory_id[$key],
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total_amount' => $request->amount[$key],
                        'created_at' => now(),
                    ]);
                    $stockList = AccessoryItem::where('accessory_id', $accessory_id)->where('type','Purchase')->where('status', 0)->orderBy('id', 'asc')->get();
                    $soldQuantity = 0;
                    foreach ($stockList as $value) {
                        $remainingQuantity = $request->quantity[$key] - $soldQuantity;
                        $availableQuantity = $value->quantity - $value->used_quantity;
                        if ($remainingQuantity >= $availableQuantity) {
                            AccessoryItemUse::create([
                                'accessory_out_id' => $accessoryOut->id,
                                'accessory_in_id' => $value->id,
                                'quantity' => $availableQuantity
                            ]);
                            AccessoryItem::where('id', $value->id)->update([
                                'status' => 1,
                                'used_quantity' => $value->used_quantity + $availableQuantity
                            ]);
                            $soldQuantity += $availableQuantity;
                        } else {
                            AccessoryItemUse::create([
                                'accessory_out_id' => $accessoryOut->id,
                                'accessory_in_id' => $value->id,
                                'quantity' => $remainingQuantity
                            ]);
                            AccessoryItem::where('id', $value->id)->update([
                                'status' => 0,
                                'used_quantity' => $value->used_quantity + $remainingQuantity
                            ]);
                            $soldQuantity += $remainingQuantity;
                            break;
                        }
                    }
                    if ($soldQuantity < $request->quantity[$key]) {
                        DB::rollBack();
                        $request->session()->flash('errorMessage', 'Insufficient stock');
                        return false;
                    }
                } else{
                    DB::rollBack();
                    $request->session()->flash('errorMessage', 'Insufficient stock');
                    return false;
                }

            }
          
        }

            DB::commit();

            $request->session()->flash('successMessage', 'Accessory Consume was successfully updated!');
            return redirect()->route('admin.accessory.consume.index');

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.accessory.consume.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete accessory-consume');
        $data = AccessoryConsume::find($id);
        $fgOutItem = AccessoryItem::where('flagable_id', $id)->where('flagable_type', AccessoryConsume::class)->where('type', 'Consume')->pluck('id');
        $fgUsage = AccessoryItemUse::whereIn('accessory_out_id', $fgOutItem)->get();
        foreach($fgUsage as $fgUsageVal){
            $fgGrnItem = AccessoryItem::where('id', $fgUsageVal->accessory_in_id)->first();
            $fgGrnItem->update([
                'status' => 0,
                'used_quantity' => $fgGrnItem->used_quantity - $fgUsageVal->quantity,
            ]);
        }
        AccessoryItemUse::whereIn('accessory_out_id', $fgOutItem)->delete();
        AccessoryItem::where('flagable_id', $id)->where('flagable_type', AccessoryConsume::class)->where('type', 'Consume')->delete();

        $data->delete();

        $request->session()->flash('successMessage', 'Accessory Purchase was successfully deleted!');
        return redirect()->route('admin.accessory.consume.index', qArray());
    }
}