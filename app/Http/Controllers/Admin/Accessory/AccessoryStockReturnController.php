<?php

namespace App\Http\Controllers\Admin\Accessory;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessoryStockReturnRequest;
use App\Models\AccessoryItem;
use App\Models\AccessoryItemUse;
use App\Models\AccessoryStockReturn;
use App\Models\accessoryIn;
use App\Models\accessoryOut;
use App\Models\AccessoryStock;
use App\Models\accessoryUse;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Services\CodeService;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AccessoryStockReturnController extends Controller
{
    protected $type = "Purchase Return";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sql = AccessoryStockReturn::orderBy('id', 'DESC')
        ->with([
            'items',
            'items.accessory',
            'createdBy',
            'updatedBy'
        ]);

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('total_amount', 'LIKE', '%'. $request->q.'%')
                ->orWhere('date', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('items.accessory', function($q) use($request) {
                $q->where('name', $request->q);
            });
            $sql->orwhereHas('createdBy', function($q) use($request) {
                $q->where('name', $request->q);
            });
        }
        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));
        return view('admin.accessory.purchase-return', compact('result'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $items = [
            (object)[
                'id' => null,
                'accessory' => null,
                'purchase_quantity' => null,
                'used_quantity' => null,
                'remain_quantity' => null,
                'quantity' => null,
                'unit_price' => null,
                'total_amount' => null
            ]
        ];
        $suppliers = Supplier::select('id','name')->where('status','Active')->get();

        return view('admin.accessory.purchase-return', compact('suppliers','items'))->with('create', 1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccessoryStockReturnRequest $request)
    {
        try {
        $code = CodeService::generate(AccessoryStockReturn::class, 'APR', 'code');
        DB::beginTransaction();
        $storeData = [
            'code' => $code,
            'supplier_id' => $request->supplier_id,
            'purchase_id' => $request->purchase_id,
            'date' => dbDateFormat($request->date),
            'subtotal_amount' => $request->subtotal_amount ?? 0,
            'total_quantity' => $request->total_quantity ?? 0,
            'total_amount' => $request->total_amount ?? 0,
            'cost' => $request->cost ?? 0,
            'note' => $request->note,
            'created_by' => auth()->user()->id
        ];

        $data = AccessoryStockReturn::create($storeData);

        foreach ($request->accessory_id as $key => $row) {
            if( array_sum($request->quantity) > 0){
                $Qty = $request->quantity[$key] ?? 0;
                $outaccessory = AccessoryItem::create(
                    [
                        'type' => $this->type,
                        'flagable_id' => $data->id,
                        'flagable_type' => AccessoryStockReturn::class,
                        'accessory_id' => $request->accessory_id[$key],
                        'quantity' => $request->quantity[$key] ?? 0,
                        'unit_price' => $request->unit_price[$key] ?? 0,
                        'total_amount' => ($request->quantity[$key] * $request->unit_price[$key]) ?? 0,
                        'created_at' => now(),
                    ]
                );
                $inaccessorys =  AccessoryItem::where('accessory_id', $request->accessory_id[$key])
                    ->where('status', 0)->get();
                    foreach($inaccessorys as $inaccessory){
                        if($Qty > 0){
                            if(($inaccessory->quantity - $inaccessory->used_quantity) < $Qty){

                                $Qty -= ($inaccessory->quantity - $inaccessory->used_quantity);
                                AccessoryItemUse::create([
                                    'accessory_in_id' => $inaccessory->id,
                                    'accessory_out_id' => $outaccessory->id,
                                    'quantity' => $inaccessory->quantity - $inaccessory->used_quantity,
                                ]);

                                $inaccessory->update([
                                    'used_quantity' => $inaccessory->quantity,
                                    'status' => 1
                                ]);

                                
                            } else if(($inaccessory->quantity - $inaccessory->used_quantity) > $Qty){
                                $inaccessory->update([
                                    'used_quantity' => $Qty + $inaccessory->used_quantity,
                                    'status' => 0
                                ]);
                                AccessoryItemUse::create([
                                    'accessory_in_id' => $inaccessory->id,
                                    'accessory_out_id' => $outaccessory->id,
                                    'quantity' => $Qty,
                                ]);
                                $Qty = 0;
                            } else {
                                $inaccessory->update([
                                    'used_quantity' => $inaccessory->quantity,
                                    'status' => 1
                                ]);
                                AccessoryItemUse::create([
                                    'accessory_in_id' => $inaccessory->id,
                                    'accessory_out_id' => $outaccessory->id,
                                    'quantity' => $Qty,
                                ]);
                                $Qty = 0;
                            }

                        }
                            
                    }
            }
        }
        DB::commit();
        $request->session()->flash('successMessage', 'Accessory Purchase Return was successfully added!');
        return redirect()->route('admin.accessory.purchase_returns.index');
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
        $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
        return redirect()->route('admin.accessory.purchase_returns.index');
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
        $data = AccessoryStockReturn::with('items')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.purchase_returns.index', qArray());
        }

        return view('admin.accessory.purchase-return', compact('data'))->with('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data = AccessoryStockReturn::with('items')->find($id);
        $items = $data->purchase->items;
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.purchase_returns.index', qArray());
        } 
        $suppliers = Supplier::select('id','name')->where('status','Active')->get();
        $purchases = AccessoryStock::select('id','date','code')->where('supplier_id', $data->supplier_id)->orderBy('id', 'DESC')->get();
        return view('admin.accessory.purchase-return', compact('items', 'suppliers', 'data','purchases'))->with('edit', $id);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccessoryStockReturnRequest $request, $id)
    {

        $data = AccessoryStockReturn::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', ' Accessory Purchase Return not found!');
            return redirect()->route('admin.accessory.purchase_returns.index', qArray());
        }

        try {
            DB::beginTransaction();

            $storeData = [
                'supplier_id' => $request->supplier_id,
                'purchase_id' => $request->purchase_id,
                'date' => dbDateFormat($request->date),
                'subtotal_amount' => $request->subtotal_amount ?? 0,
                'total_quantity' => $request->total_quantity ?? 0,
                'total_amount' => $request->total_amount ?? 0,
                'cost' => $request->cost ?? 0,
                'note' => $request->note,
                'updated_by' => auth()->user()->id
            ];
            $data->update($storeData);
            $fgOutItem = AccessoryItem::where('flagable_id', $id)->where('flagable_type', AccessoryStockReturn::class)->where('type', $this->type)->pluck('id');
            $fgUsage = AccessoryItemUse::whereIn('accessory_out_id', $fgOutItem)->get();
            foreach($fgUsage as $fgUsageVal){
                $fgGrnItem = AccessoryItem::where('id', $fgUsageVal->accessory_in_id)->first();
                $fgGrnItem->update([
                    'status' => 0,
                    'used_quantity' => $fgGrnItem->used_quantity - $fgUsageVal->quantity,
                ]);
            }
            AccessoryItemUse::whereIn('accessory_out_id', $fgOutItem)->delete();
            AccessoryItem::where('flagable_id', $id)->where('flagable_type', AccessoryStockReturn::class)->where('type', $this->type)->delete();

            foreach ($request->accessory_id as $key => $row) {
                if( array_sum($request->quantity) > 0){
                    $Qty = $request->quantity[$key] ?? 0;
                    $outaccessory = AccessoryItem::create(
                        [
                            'type' => $this->type,
                            'flagable_id' => $data->id,
                            'flagable_type' => AccessoryStockReturn::class,
                            'accessory_id' => $request->accessory_id[$key],
                            'quantity' => $request->quantity[$key] ?? 0,
                            'unit_price' => $request->unit_price[$key] ?? 0,
                            'total_amount' => ($request->quantity[$key] * $request->unit_price[$key]) ?? 0,
                            'created_at' => now(),
                        ]
                    );
                    $inaccessorys =  AccessoryItem::where('accessory_id', $request->accessory_id[$key])
                        ->where('status', 0)->get();
                        foreach($inaccessorys as $inaccessory){
                            if($Qty > 0){
                                if(($inaccessory->quantity - $inaccessory->used_quantity) < $Qty){
    
                                    $Qty -= ($inaccessory->quantity - $inaccessory->used_quantity);
                                    AccessoryItemUse::create([
                                        'accessory_in_id' => $inaccessory->id,
                                        'accessory_out_id' => $outaccessory->id,
                                        'quantity' => $inaccessory->quantity - $inaccessory->used_quantity,
                                    ]);
    
                                    $inaccessory->update([
                                        'used_quantity' => $inaccessory->quantity,
                                        'status' => 1
                                    ]);
    
                                    
                                } else if(($inaccessory->quantity - $inaccessory->used_quantity) > $Qty){
                                    $inaccessory->update([
                                        'used_quantity' => $Qty + $inaccessory->used_quantity,
                                        'status' => 0
                                    ]);
                                    AccessoryItemUse::create([
                                        'accessory_in_id' => $inaccessory->id,
                                        'accessory_out_id' => $outaccessory->id,
                                        'quantity' => $Qty,
                                    ]);
                                    $Qty = 0;
                                } else {
                                    $inaccessory->update([
                                        'used_quantity' => $inaccessory->quantity,
                                        'status' => 1
                                    ]);
                                    AccessoryItemUse::create([
                                        'accessory_in_id' => $inaccessory->id,
                                        'accessory_out_id' => $outaccessory->id,
                                        'quantity' => $Qty,
                                    ]);
                                    $Qty = 0;
                                }
    
                            }
                                
                        }
                }
            }
            DB::commit();

            $request->session()->flash('successMessage', ' Accessory Purchase Return was successfully updated!');
            return redirect()->route('admin.accessory.purchase_returns.index');

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.accessory.purchase_returns.index');
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
        $data = AccessoryStockReturn::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.purchase_returns.index', qArray());
        }

        DB::beginTransaction();
        try{

            $fgOutItem = AccessoryItem::where('flagable_id', $id)->where('flagable_type', AccessoryStockReturn::class)->where('type', $this->type)->pluck('id');
            $fgUsage = AccessoryItemUse::whereIn('accessory_out_id', $fgOutItem)->get();
            foreach($fgUsage as $fgUsageVal){
                $fgGrnItem = AccessoryItem::where('id', $fgUsageVal->accessory_in_id)->first();
                $fgGrnItem->update([
                    'status' => 0,
                    'used_quantity' => $fgGrnItem->used_quantity - $fgUsageVal->quantity,
                ]);
            }
            AccessoryItemUse::whereIn('accessory_out_id', $fgOutItem)->delete();
            AccessoryItem::where('flagable_id', $id)->where('flagable_type', AccessoryStockReturn::class)->where('type', $this->type)->delete();

            $data->delete();
            DB::commit();
            $request->session()->flash('successMessage', ' Return was successfully deleted!');
            return redirect()->route('admin.accessory.purchase_returns.index', qArray());
        }catch (\Exception $e){
            DB::rollBack();
            $request->session()->flash('errorMessage', ' Return was not deleted! ' . $e);
            return redirect()->route('admin.accessory.purchase_returns.index', qArray());
        }
    }
}