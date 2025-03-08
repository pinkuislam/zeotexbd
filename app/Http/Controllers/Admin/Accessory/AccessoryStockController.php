<?php

namespace App\Http\Controllers\Admin\Accessory;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessoryStockRequest;
use App\Models\Accessory;
use App\Models\AccessoryItem;
use App\Models\AccessoryStock;
use App\Models\Supplier;
use App\Services\CodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MediaUploader;

class AccessoryStockController extends Controller
{
    protected $type = "Purchase";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list accessory-purchase');
        $sql = AccessoryStock::orderBy('id', 'DESC')->with(['items']);

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('date', 'LIKE', '%'. $request->q.'%')
                ->orWhere('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('note', 'LIKE', '%'. $request->q.'%')
                ->orWhere('total_quantity', 'LIKE', '%'. $request->q.'%')
                ->orWhere('total_amount', 'LIKE', '%'. $request->q.'%');
            });
        }

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.accessory.purchase', compact('result'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add accessory-purchase');
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
        $suppliers = Supplier::where('status','Active')->get();
        return view('admin.accessory.purchase', compact('items', 'accessories','suppliers'))->with('create', 1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   
    public function store(AccessoryStockRequest $request)
    {
        $this->authorize('add accessory-purchase');
        $imageName = '';
        if ($request->hasFile('challan_image')) {
            $file = MediaUploader::imageUpload($request->challan_image, 'challan', 1, null, [600, 600], [80, 80]);
            $imageName = $file['name'];
        }
        $code = CodeService::generate(AccessoryStock::class, 'AS', 'code');
        try {
            DB::beginTransaction();
            $storeData = [
                'code' => $code,
                'date' => dbDateFormat($request->date),
                'challan_number' => $request->challan_number,
                'challan_image' => $imageName,
                'note' => $request->note,
                'supplier_id' => $request->supplier_id,
                'subtotal_amount' => $request->subtotal_amount ?? 0,
                'total_quantity' => $request->total_quantity ?? 0,
                'total_amount' => $request->total_amount ?? 0,
                'cost' => $request->cost ?? 0,
                'created_by' => auth()->user()->id
            ];

            $data = AccessoryStock::create($storeData);

            foreach ($request->accessory_id as $key => $row) {
                if($request->quantity[$key] > 0){
                    AccessoryItem::create(
                        [
                            'type' => $this->type,
                            'flagable_id' => $data->id,
                            'flagable_type' => AccessoryStock::class,
                            'accessory_id' => $request->accessory_id[$key],
                            'quantity' => $request->quantity[$key],
                            'used_quantity' => 0,
                            'unit_price' => $request->unit_price[$key],
                            'total_amount' => $request->amount[$key],
                            'created_at' => now(),
                        ]
                    );
                }
            }
            DB::commit();
            $request->session()->flash('successMessage', 'Accessory Purchase was successfully added!');
            return redirect()->route('admin.accessory.purchase.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.accessory.purchase.index');
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
        $this->authorize('show accessory-purchase');
        $data = AccessoryStock::with('items')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.purchase.index', qArray());
        }

        return view('admin.accessory.purchase', compact('data'))->with('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->authorize('edit accessory-purchase');
        $data = AccessoryStock::with('items')->find($id);
        $items = $data->items;
        $accessories = Accessory::where('status','Active')->get();
        $suppliers = Supplier::where('status','Active')->get();
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.purchase.index', qArray());
        }
        return view('admin.accessory.purchase', compact('data','accessories','suppliers','items'))->with('edit', $id);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccessoryStockRequest $request, $id)
    {
        $this->authorize('edit accessory-purchase');
        $data = AccessoryStock::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Accessory Purchase not found!');
            return redirect()->route('admin.accessory.purchase.index', qArray());
        }

        try {
            DB::beginTransaction();
            $storeData = [
                'date' => dbDateFormat($request->date),
                'challan_number' => $request->challan_number,
                'note' => $request->note,
                'supplier_id' => $request->supplier_id,
                'subtotal_amount' => $request->subtotal_amount ?? 0,
                'total_quantity' => $request->total_quantity ?? 0,
                'cost' => $request->cost ?? 0,
                'total_amount' => $request->total_amount ?? 0,
                'updated_by' => auth()->user()->id
            ];
            if ($request->hasFile('challan_image')) {
                if($data->challan_image) {
                    MediaUploader::delete('challan', $data->challan_image, true);
                }
                $file = MediaUploader::imageUpload($request->challan_image, 'challan', 1, null, [600, 600], [80, 80]);
                $storeData['challan_image'] = $file['name'];
            }
            $data->update($storeData);
            if ($data && count($request->accessory_id) > 0 ) {
                AccessoryItem::whereNotIn('accessory_id', $request->accessory_id)->where('flagable_id', $data->id)->where('flagable_type', AccessoryStock::class)->delete();      
               foreach ($request->accessory_id as $key => $product) {
                   $updateItemData = [
                        'type' => $this->type,
                        'flagable_id' => $data->id,
                        'flagable_type' => AccessoryStock::class,
                        'accessory_id' => $request->accessory_id[$key],
                        'quantity' => $request->quantity[$key],
                        'used_quantity' => 0,
                        'unit_price' => $request->unit_price[$key],
                        'total_amount' => $request->amount[$key],
                        'updated_at' => now(),
                   ];
                  $item = AccessoryItem::where('accessory_id', $request->accessory_id[$key])->where('flagable_id', $data->id)->where('flagable_type', AccessoryStock::class)->first();
                  if ($item) {
                       $item->update($updateItemData);
                  }else {
                       AccessoryItem::create($updateItemData);
                  }
               }
           }
            DB::commit();

            $request->session()->flash('successMessage', 'Accessory Purchase was successfully updated!');
            return redirect()->route('admin.accessory.purchase.index');

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.accessory.purchase.index');
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
        $this->authorize('delete accessory-purchase');
        $data = AccessoryStock::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.purchase.index', qArray());
        }
        AccessoryItem::where('flagable_id', $data->id)->where('flagable_type', AccessoryStock::class)->delete();      
        if($data->challan_image) {
            MediaUploader::delete('challan', $data->challan_image, true);
        }
        $data->delete();

        $request->session()->flash('successMessage', 'Accessory Purchase was successfully deleted!');
        return redirect()->route('admin.accessory.purchase.index', qArray());
    }

    public function getSupplierPurchase(Request $request)
    {
        $data = AccessoryStock::with('items')->select('id','date','code')->where('supplier_id', $request->id)->get();
        return response()->json(['success' => true, 'data' => $data]);

    }
    public function getPurchase(Request $request)
    {
        $data = AccessoryStock::with(['items.accessory'])->find($request->id);
        return response()->json(['success' => true, 'data' => $data]);

    }
}