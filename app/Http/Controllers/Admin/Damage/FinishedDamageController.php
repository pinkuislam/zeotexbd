<?php

namespace App\Http\Controllers\Admin\Damage;

use App\Http\Controllers\Controller;
use App\Http\Requests\DamageRequest;
use App\Models\Color;
use App\Models\Damage;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\ProductOut;
use App\Models\ProductUse;
use App\Models\Unit;
use App\Services\CodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinishedDamageController extends Controller
{
    protected $type = "Damage";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list damage');
        $sql = Damage::where('type','Finished')->orderBy('id', 'DESC')->with('items','items.product','createdBy','updatedBy');
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('note', 'LIKE', '%'. $request->q.'%')
                ->orWhere('date', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('items.product', function($q) use($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('createdBy', function($q) use($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }
        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.damage.finished', compact('result'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add damage');
        $items = [
            (object)[
                'id' => null,
                'product_id' => null,
                'unit_id' => null,
                'color_id' => null,
                'quantity' => null
            ]
        ];

        $colors = Color::where('status','Active')->get();
        $units = Unit::where('status','Active')->get();
        $products = Product::where(['status' => 'Active'])->whereIn('product_type', ['Base', 'Product', 'Combo'])->with('unit')->get();

        return view('admin.damage.finished', compact('items', 'products', 'units', 'colors'))->with('create', 1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DamageRequest $request)
    {
        $this->authorize('add damage');
        $code = CodeService::generate(Damage::class, 'DRM', 'code');
        try {
            DB::beginTransaction();
            $storeData = [
                'code' => $code,
                'date' => dbDateFormat($request->date),
                'note' => $request->note,
                'type' => 'Finished',
                'created_by' => auth()->user()->id
            ];

            $data = Damage::create($storeData);

            foreach ($request->product_id as $key => $row) {
                $product = Product::where('id',$request->product_id[$key])->first();
                $finishedQty = $request->quantity[$key];
                if($request->quantity[$key] > 0){
                    $itemDataDam = [
                        'type' => $this->type,
                        'flagable_id' => $data->id,
                        'flagable_type' => Damage::class,
                        'product_id' => $request->product_id[$key],
                        'unit_id' => $request->unit_id[$key] ,
                        'color_id' => $request->color_id[$key] ,
                        'quantity' => $finishedQty ?? 0,
                        'unit_price' => $product->unit_price,
                        'total_price' => $finishedQty * $product->unit_price,
                        'created_at' => now(),
                    ];
                    $outproduct = ProductOut::create($itemDataDam);
                    $inproducts =  ProductIn::where('product_id', $request->product_id[$key])
                    ->where('color_id', $request->color_id[$key])
                    ->where('status', 0)->get();
                    foreach($inproducts as $inproduct){
                        if($finishedQty > 0){
                            if(($inproduct->quantity - $inproduct->used_quantity) < $finishedQty){

                                $finishedQty -= ($inproduct->quantity - $inproduct->used_quantity);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $inproduct->quantity - $inproduct->used_quantity,
                                ]);

                                $inproduct->update([
                                    'used_quantity' => $inproduct->quantity,
                                    'status' => 1
                                ]);
                                
                            } else if(($inproduct->quantity - $inproduct->used_quantity) > $finishedQty){
                                $inproduct->update([
                                    'used_quantity' => $finishedQty + $inproduct->used_quantity,
                                    'status' => 0
                                ]);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $finishedQty,
                                ]);
                                $finishedQty = 0;
                            } else {
                                $inproduct->update([
                                    'used_quantity' => $inproduct->quantity,
                                    'status' => 1
                                ]);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $finishedQty,
                                ]);
                                $finishedQty = 0;
                            }
                        }    
                    }
                }
            }
            DB::commit();
            $request->session()->flash('successMessage', 'Finished Damage was successfully added!');
            return redirect()->route('admin.damage.finished.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.damage.finished.index');
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
        $this->authorize('show damage');
        $data = Damage::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.damage.finished.index', qArray());
        }

        return view('admin.damage.finished', compact('data'))->with('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->authorize('edit damage');
        $data = Damage::with('items')->find($id);
        $items = $data->items;
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.damage.finished.index', qArray());
        } else {
            //check used quantity
            foreach($data->items as $item){
                if($item->used_quantity > 0){
                    $request->session()->flash('errorMessage', 'This Raw items already consumed !!');
                    return redirect()->route('admin.damage.finished.index', qArray());
                }
            }
        }
        $colors = Color::where('status','Active')->get();
        $units = Unit::where('status','Active')->get();
        $products = Product::where(['status' => 'Active'])->whereIn('product_type', ['Base', 'Product', 'Combo'])->with('unit')->get();
        return view('admin.damage.finished', compact('items', 'products', 'units', 'colors', 'data'))->with('edit', $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('edit damage');
        try {
            DB::beginTransaction();
            $data = Damage::find($id);
            $storeData = [
                'date' => dbDateFormat($request->date),
                'note' => $request->note,
                'type' => 'Finished',
                'updated_by' => auth()->user()->id
            ];
    
            $data->update($storeData);

            $damageUsed = ProductUse::where('product_outs.flagable_id', $id)->where('product_outs.flagable_type', Damage::class)
            ->select('product_uses.*')
            ->join('product_outs', 'product_uses.product_out_id', '=', 'product_outs.id')
            ->get();
            foreach($damageUsed as $value){
                $info = ProductIn::find($value->product_in_id);
                ProductIn::where('id', $value->product_in_id)->update(['used_quantity' => ($info->used_quantity - $value->quantity), 'status'=> 0 ]);
            }

            $usesIds = $damageUsed->pluck('id');
            ProductUse::whereIn('id', $usesIds)->delete();
            ProductOut::where('flagable_id', $data->id)->where('flagable_type', Damage::class)->delete();
           
            foreach ($request->product_id as $key => $row) {
                $product = Product::where('id',$request->product_id[$key])->first();
                $finishedQty = $request->quantity[$key];
                if($request->quantity[$key] > 0){
                    $itemDataDam = [
                        'type' => $this->type,
                        'flagable_id' => $data->id,
                        'flagable_type' => Damage::class,
                        'product_id' => $request->product_id[$key],
                        'unit_id' => $request->unit_id[$key] ,
                        'color_id' => $request->color_id[$key] ,
                        'quantity' => $finishedQty ?? 0,
                        'unit_price' => $product->unit_price,
                        'total_price' => $finishedQty * $product->unit_price,
                        'created_at' => now(),
                    ];
                    $outproduct = ProductOut::create($itemDataDam);
                    $inproducts =  ProductIn::where('product_id', $request->product_id[$key])
                    ->where('color_id', $request->color_id[$key])
                    ->where('status', 0)->get();
                    foreach($inproducts as $inproduct){
                        if($finishedQty > 0){
                            if(($inproduct->quantity - $inproduct->used_quantity) < $finishedQty){
    
                                $finishedQty -= ($inproduct->quantity - $inproduct->used_quantity);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $inproduct->quantity - $inproduct->used_quantity,
                                ]);
    
                                $inproduct->update([
                                    'used_quantity' => $inproduct->quantity,
                                    'status' => 1
                                ]);
                                
                            } else if(($inproduct->quantity - $inproduct->used_quantity) > $finishedQty){
                                $inproduct->update([
                                    'used_quantity' => $finishedQty + $inproduct->used_quantity,
                                    'status' => 0
                                ]);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $finishedQty,
                                ]);
                                $finishedQty = 0;
                            } else {
                                $inproduct->update([
                                    'used_quantity' => $inproduct->quantity,
                                    'status' => 1
                                ]);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $finishedQty,
                                ]);
                                $finishedQty = 0;
                            }
                        }    
                    }
                }
            }
            DB::commit();
            $request->session()->flash('successMessage', 'Finished Damage was successfully updated!');
            return redirect()->route('admin.damage.finished.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.damage.finished.index');
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
        $this->authorize('delete damage');
        $data = Damage::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.damage.finished.index', qArray());
        }

        DB::beginTransaction();
        try{

            $damageUsed = ProductUse::where('product_outs.flagable_id', $id)->where('product_outs.flagable_type', Damage::class)
            ->select('product_uses.*')
            ->join('product_outs', 'product_uses.product_out_id', '=', 'product_outs.id')
            ->get();
            foreach($damageUsed as $value){
                $info = ProductIn::find($value->product_in_id);
                ProductIn::where('id', $value->product_in_id)->update(['used_quantity' => ($info->used_quantity - $value->quantity), 'status'=> 0 ]);
            }

            $usesIds = $damageUsed->pluck('id');
            ProductUse::whereIn('id', $usesIds)->delete();
            ProductOut::where('flagable_id', $data->id)->where('flagable_type', Damage::class)->delete();
            $data->delete();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Finished Damage was not deleted! ' . $e);
            return redirect()->route('admin.damage.finished.index', qArray());
        }

        $request->session()->flash('successMessage', 'Finished Damage was successfully deleted!');
        return redirect()->route('admin.damage.finished.index', qArray());
    }
}