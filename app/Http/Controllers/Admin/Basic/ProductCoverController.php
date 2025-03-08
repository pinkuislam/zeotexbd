<?php

namespace App\Http\Controllers\Admin\Basic;

use App\Models\User;
use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MediaController;
use App\Http\Requests\ProdcutRequest;
use Illuminate\Support\Str;
use App\Models\Color;
use App\Models\ProductBase;
use App\Models\ProductFabric;
use App\Models\ProductItem;
use App\Models\ProductOtherImage;
use App\Models\ProductOtherInfo;
use App\Models\Size;
use App\Services\CategoryService;
use App\Services\CodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Storage;
use Sudip\MediaUploader\Facades\MediaUploader;

class ProductCoverController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list product');

        $sql = Product::with('unit')->where('master_type', 'Cover')->orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%')
                ->orWhere('code', 'LIKE', '%'. $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }
        if ($request->product_type) {
            $sql->where('product_type', $request->product_type);
        }

        
        if ($request->category) {
            $sql->where('category_type', $request->category);
        }

        $data['products'] = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.basic.product-cover', $data)->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add product');
        $data['units'] = Unit::where('status','Active')->get();
        $data['products'] = Product::with(['unit','items'])->where('status', 'Active')->where('master_type', 'Cover')->where('product_type', 'Fabric')->orderBy('created_at', 'DESC')->get();
        $data['items'] = [
            (object)[
                'id' => null,
                'product_id' => null,
                'quantity' => null,
                'base_id' => null,
            ]
        ];

        return view('admin.basic.product-cover', $data)->with('create', 1);
    }

    public function store(ProdcutRequest $request)
    {
        $this->authorize('add product');
      try {
        
        $code = CodeService::generate(Product::class, 'P', 'code');
        $stockPrice = $request->stock_price ?? 0;
        $salePrice = $request->sale_price ?? 0;
        $resellerPrice = $request->reseller_price ?? 0;
        $storeData = [
            'name' => $request->name,
            'code' => $code,
            'unit_id' => $request->unit_id,
            'master_type' => 'Cover',
            'product_type' => $request->product_type,
            'category_type' => $request->category ?? 'Regular',
            'alert_quantity' => $request->alert_quantity,
            'stock_price' => $stockPrice,
            'sale_price' => $salePrice,
            'reseller_price' => $resellerPrice,
            'status' => $request->status,
            'created_by' => Auth()->user()->id
        ];

        if($request->seat_count){
            $storeData['seat_count'] = $request->seat_count;
        }

        $data = Product::create($storeData);

        if($data->product_type == 'Base'  || $request->product_type == 'Base-Ready-Production') {

            $data->prodductUsers()->sync($this->mapReseller($data->reseller_price));
        }

        if ($request->product_type == 'Base' || $request->product_type == 'Base-Ready-Production') {
            $fabricProduct = Product::where('id', $request->fabric_product_id)->first();
            $productFabric ['product_id'] = $data->id;
            $productFabric ['fabric_product_id'] = $fabricProduct->id;
            $productFabric ['fabric_unit_id'] = $fabricProduct->unit_id;
            $productFabric ['fabric_quantity'] = $request->fabric_quantity;
            ProductFabric::create($productFabric);
        }else if ($request->product_type == 'Combo') { 
            $productFabric ['product_id'] = $data->id;
            $productFabric ['fabric_product_id'] = $request->fabric_base_product_id;
            $productFabric ['fabric_unit_id'] = null;
            $productFabric ['fabric_quantity'] = 0;
            ProductFabric::create($productFabric);
        } 
        

        if ($request->product_type == 'Combo' && count($request->product_id) > 0) {
            $mapResellerPrice = 0;
            foreach ($request->product_id as $key => $product) {

                ProductBase::create([
                    'product_id' => $data->id,
                    'base_id' => $product,
                    'quantity' => $request->quantity[$key]
                ]);

                $base = Product::find($product);
                $mapResellerPrice += $base->reseller_price *  $request->quantity[$key];
            }

            $data->prodductUsers()->sync($this->mapReseller($mapResellerPrice));
        }

        $request->session()->flash('successMessage', 'Product was successfully added!');
        return redirect()->route('admin.basic.product-cover.create', qArray());
      } catch (\Exception $e) {
        $request->session()->flash('errorMessage', $e->getMessage());
        return redirect()->route('admin.basic.product-cover.create', qArray());
      }
    }



    public function show(Request $request, $id)
    {
        $this->authorize('show products');
        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.product.index', qArray());
        }

        return view('admin.basic.product-cover', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit product');
        $product = Product::with('items','item')->find($id);
        if (empty($product)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.product-cover.index', qArray());
        }
        $data['data'] = $product;
        $data['products'] = Product::where('status', 'Active')
            ->where('master_type', 'Cover')
            ->where('product_type', 'Fabric')
            ->orderBy('created_at', 'DESC')
            ->get();
        $data['fabric_products'] = Product::where('status', 'Active')
            ->where('product_type', 'Fabric')
            ->orderBy('created_at', 'DESC')->get();
        if($product->item){
            $data['fabric_wise_base_products']= Product::select('products.*')->whereIn('product_type', ['Base', 'Base-Ready-Production'])
                ->join('product_fabrics', 'product_fabrics.product_id', '=', 'products.id')
                ->where('product_fabrics.fabric_product_id', $product->item->fabric_product_id)
                ->where('category_type', $product->category_type)
                ->get();
        }
        
        if (count($product->items) > 0) {
            $data['items'] = $product->items;
        } else {
            $data['items'] = [
                (object)[
                    'id' => null,
                    'base_id' => null,
                    'quantity' => null,
                    'product_id' => null,
                ]
            ];
        }
        $data['units'] = Unit::where('status','Active')->get();
        //dd($data);
        return view('admin.basic.product-cover', $data)->with('edit', $id);
    }

    public function update(ProdcutRequest $request, $id)
    {
        $this->authorize('edit product');

        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.product-cover.index', qArray());
        }

        $stockPrice = $request->stock_price ?? 0;
        $salePrice = $request->sale_price ?? 0;
        $resellerPrice = $request->reseller_price ?? 0;

        $storeData = [
            'name' => $request->name,
            'unit_id' => $request->unit_id,
            'master_type' => 'Cover',
            'product_type' => $request->product_type,
            'category_type' => $request->category,
            'alert_quantity' => $request->alert_quantity,
            'stock_price' => $stockPrice,
            'sale_price' => $salePrice,
            'reseller_price' => $resellerPrice,
            'status' => $request->status,
            'updated_by' => Auth()->user()->id,
        ];

        if($request->seat_count){
            $storeData['seat_count'] = $request->seat_count;
        }

        if ($request->product_type == 'Base' || $request->product_type == 'Base-Ready-Production') {
            
            $fabricProduct = Product::where('id', $request->fabric_product_id)->first();
            ProductFabric::updateOrCreate([
                'product_id' => $data->id
            ],[
                'fabric_product_id' => $fabricProduct->id,
                'fabric_unit_id' => $fabricProduct->unit_id,
                'fabric_quantity' => $request->fabric_quantity
            ]);
        }else if ($request->product_type == 'Combo') { 
            ProductFabric::updateOrCreate([
                'product_id' => $data->id
            ],[
                'fabric_product_id' => $request->fabric_base_product_id,
                'fabric_unit_id' => null,
                'fabric_quantity' => 0
            ]);
        } 

        $resellerPriceUpdated = false;

        if($request->product_type != 'Fabric') {
            if($data->reseller_price != $request->reseller_price){
                $resellerPriceUpdated = true;
            }
        }

        $data->update($storeData);

        if($request->product_type != 'Fabric' && $resellerPriceUpdated){
            if($data->product_type == 'Base'  || $request->product_type == 'Base-Ready-Production') {
                $data->prodductUsers()->sync($this->mapReseller($data->reseller_price));
            }
        }


        if ($request->product_type == 'Combo' && count($request->product_id) > 0) {
            $itemIdArr = [];
            $mapResellerPrice = 0;
            foreach ($request->product_id as $key => $product) {
                $item = [
                    'id' => $request->item_id[$key],
                    'product_id' => $data->id,
                    'base_id' => $product,
                    'quantity' => $request->quantity[$key]
                ];
                $itemIdArr[] = $data->items()->updateOrCreate(['id' => $item['id']], $item)->id;

                $base = Product::find($product);
                $mapResellerPrice += $base->reseller_price *  $request->quantity[$key];
            }

            $data->prodductUsers()->sync($this->mapReseller($mapResellerPrice));

            //Delete removed item from db...
            $data->items()->whereNotIn('id', $itemIdArr)->delete();
        }
        $request->session()->flash('successMessage', 'Product was successfully updated!');
        return redirect()->route('admin.basic.product-cover.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete product');
        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.product-cover.index', qArray());
        }

        (new MediaController())->delete('products', $data->image);
        ProductBase::where('product_id', $data->id)->get()->each->delete();
        $data->delete();

        $request->session()->flash('successMessage', 'Product was successfully deleted!');
        return redirect()->route('admin.basic.product-cover.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('status product');

        $data = Product::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        $request->session()->flash('successMessage', 'Product status was successfully changed!');
        return redirect()->route('admin.basic.product-cover.index', qArray());
    }

    
    public function getRawProduct(Request $request)
    {
        $res = Product::with('unit')->where('product_type', 'Fabric')->get();
        if ($res) {
            return response()->json(['success' => true, 'data' => $res]);
        }
        return response()->json(['success' => false]);
    }
    public function getBaseProduct(Request $request)
    {
        $res = Product::select('products.*')->where('product_type', 'Base')
        ->join('product_fabrics', 'product_fabrics.product_id', '=', 'products.id')
        ->where('product_fabrics.fabric_product_id', $request->id)
        ->where('category_type', $request->category)->get();
        if ($res) {
            return response()->json(['success' => true, 'data' => $res]);
        }
        return response()->json(['success' => false]);
    }
    public function ecommerce($id)
    {
        $this->authorize('ecommerce_setup product');
        $categories = CategoryService::get(true);
        $colors = Color::select('id', 'name')->where('status', 'Active')->get();
        $sizes = Size::select('id', 'name')->where('status', 'Active')->get();
        $data = Product::with('otherinfo','productItems','otherImages')->findOrFail($id);
        if ($data->productItems->count() > 0) {
            $items = $data->productItems;
        } else {
            $items = [
                (object) [
                    'id' => null,
                    'color_id' => null,
                    'size_id' => null,
                    'barcode' => null,
                    'old_price' => null,
                    'sale_price' => null,
                ]
            ];
        }
        if ($data->otherImages->count() > 0) {
            $otherImages = $data->otherImages;
        } else {
            $otherImages = [
                (object)[
                    'id' => 0,
                    'image' => null,
                ]
            ];
        }

        return view('admin.basic.cover-products-ecommerce', compact('categories', 'colors', 'sizes', 'items', 'otherImages','data'))->with('create', 1);
    }

    public function ecommerceStore(Request $request)
    {
        $this->authorize('ecommerce_setup product');
        $info = ProductOtherInfo::where('product_id', $request->product_id)->first();
        $infoData = [
            'product_id' => $request->product_id,
            'category_id' => $request->category_id,
            'slug' => Str::slug($request->name),
            'short_description' => $request->short_description,
            'video_link' => $request->video_link,
            'description' => $request->description,
            'is_new_arrival' => $request->is_new_arrival,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
        ];
        if ($request->image_is_removed == 1) {
            MediaUploader::delete('products', $info->image, 1);   //Delete Old File
            $infoData['image'] = null;
            $infoData['image_url'] = null;
        }

        if ($request->hasFile('image')) {
            $file = MediaUploader::anyUpload($request->image, 'products', null, null, [640, 640]);
            if ($file) {
                $infoData['image'] = $file['name'];
            }
        }
        if ($info) {
            $info->update($infoData);
        }else {
            ProductOtherInfo::create($infoData);
        }

        $rowIds = [];
        foreach ($request->items as $item) {
            $itemData = [
                'product_id' => $request->product_id,
                'barcode' => isset($item['barcode']) ? $item['barcode'] : $this->generateBarcode(),
                'color_id' => $item['color_id'],
                'size_id' => $item['size_id'],
                'old_price' => $item['old_price'],
                'sale_price' => $item['sale_price'],
            ];
            $row = ProductItem::updateOrCreate(['id' => $item['row_id']], $itemData);

            $rowIds[] = $row->id;
        }
        ProductItem::where('product_id', $request->product_id)->whereNotIn('id', array_filter($rowIds))->delete();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $ik => $img) {
                if ($request->image_ids[$ik] > 0) {
                    $oldRow = ProductOtherImage::find($request->image_ids[$ik]);
                    MediaUploader::delete('products/' . $request->product_id, $oldRow->image);

                    $file = MediaUploader::anyUpload($img, 'products/' . $request->product_id, false, null,  [640, 640]);
                    $oldRow->update(
                        [
                            'image' => $file['name'],
                        ]
                    );
                } else {
                    $file = MediaUploader::anyUpload($img, 'products/' . $request->product_id, false, null,  [640, 640]);
                    ProductOtherImage::create([
                        'product_id' => $request->product_id,
                        'image' => $file['name'],
                    ]);
                }
            }
        }
        $request->session()->flash('successMessage', 'Product was successfully updated.');
        return back();
    }
    public function generateBarcode() {
        $barcode = '';
        for ($i = 0; $i < 11; $i++) {
            $barcode .= mt_rand(1,9);
        }
        return $barcode;
    }
    public function destroyImage(Request $request)
    {
        $validator = Validator::make($request->only('id'), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(", ", $validator->messages()->all())], 401);
        }

        $data = ProductOtherImage::find($request->id);
        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'Image not found!']);
        }

        MediaUploader::delete('products/' . $data->product_id, $data->image);

        $data->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }
    public function destroyEcommerceData($id)
    {
        $this->authorize('ecommerce_setup product');

        $data = ProductOtherInfo::where('product_id', $id)->first();
        if (empty($data)) {
            session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.product-cover.index', qArray());
        }
        if ($data->image) {
            MediaUploader::delete('products', $data->image);
        }
        $galleries = ProductOtherImage::where('product_id',$id)->get();
        if (count($galleries) > 0) {
            foreach ($galleries as  $gallery) {
                if ($gallery->image) {
                    MediaUploader::delete('products/' . $data->product_id, $gallery->image);
                }
                $gallery->delete();
            }
            Storage::deleteDirectory('products/' .$data->product_id);
        }
        $data->delete();
        session()->flash('successMessage', 'Ecommerce data deleted successfully');
        return redirect()->route('admin.basic.product-cover.index', qArray());
    }

    private function mapReseller($price)
    {
        $users = User::where('role', 'Reseller')->pluck('id', 'id');
        return $users->map(function ($i) use($price) {
            return ['price' => $price];
        });
    }
}
