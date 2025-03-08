<?php

namespace App\Http\Controllers\Admin\Basic;

use App\Models\User;
use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\MediaController;
use App\Http\Requests\ProdcutRequest;
use Illuminate\Support\Str;
use App\Models\ProductBase;
use App\Models\Color;
use App\Models\ProductItem;
use App\Models\ProductOtherImage;
use App\Models\ProductOtherInfo;
use App\Models\Size;
use App\Services\CategoryService;
use App\Services\CodeService;
use Illuminate\Support\Facades\Auth;
use Storage;
use Sudip\MediaUploader\Facades\MediaUploader;

class ProductOtherController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list product');

        $sql = Product::with('unit')->where('master_type', 'Other')->orderBy('id', 'DESC');

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
        $data['products'] = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.basic.product-other', $data)->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add product');
        $data['units'] = Unit::where('status','Active')->get();
        $data['products'] = Product::with(['unit','items'])->where('status', 'Active')->where('product_type', 'Product')->get();
        $data['categories'] = CategoryService::get(true);
        $data['items'] = [
            (object)[
                'id' => null,
                'product_id' => null,
                'quantity' => null,
                'base_id' => null,
            ]
        ];

        return view('admin.basic.product-other', $data)->with('create', 1);
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
            'master_type' => 'Other',
            'product_type' => $request->product_type,
            'category_id' => $request->category_id,
            'alert_quantity' => $request->alert_quantity,
            'stock_price' => $stockPrice,
            'sale_price' => $salePrice,
            'reseller_price' => $resellerPrice,
            'status' => $request->status,
            'created_by' => Auth()->user()->id
        ];
        $data = Product::create($storeData);

        if($data->product_type == 'Product') {
            $data->prodductUsers()->sync($this->mapReseller($data->reseller_price));
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
        return redirect()->route('admin.basic.product-other.create', qArray());
      } catch (\Exception $e) {
        $request->session()->flash('errorMessage', $e->getMessage());
        return redirect()->route('admin.basic.product-other.create', qArray());
      }
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show product');
        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.product.index', qArray());
        }

        return view('admin.basic.product-other', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit product');
        $product = Product::with('items')->find($id);
        if (empty($product)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.product-other.index', qArray());
        }
        $data['data'] = $product;
        
        $data['units'] = Unit::where('status','Active')->get();
        $data['products'] = Product::with(['unit','items'])->where('status', 'Active')->where('product_type', 'Product')->get();
        $data['categories'] = CategoryService::get(true);
        if (count($product->items) > 0) {
            $data['items'] = $product->items;
        } else {
            $data['items'] = [
                (object)[
                    'id' => null,
                    'product_id' => null,
                    'quantity' => null,
                    'base_id' => null,
                ]
            ];
        }
        return view('admin.basic.product-other', $data)->with('edit', $id);
    }

    public function update(ProdcutRequest $request, $id)
    {
        $this->authorize('edit product');

        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.product-other.index', qArray());
        }

        $stockPrice = $request->stock_price ?? 0;
        $salePrice = $request->sale_price ?? 0;
        $resellerPrice = $request->reseller_price ?? 0;

        $storeData = [
            'name' => $request->name,
            'unit_id' => $request->unit_id,
            'master_type' => 'Other',
            'product_type' => $request->product_type,
            'category_id' => $request->category_id,
            'alert_quantity' => $request->alert_quantity,
            'stock_price' => $stockPrice,
            'sale_price' => $salePrice,
            'reseller_price' => $resellerPrice,
            'status' => $request->status,
            'updated_by' => Auth()->user()->id,
        ];

        $resellerPriceUpdated = false;

        if($data->reseller_price != $request->reseller_price){
            $resellerPriceUpdated = true;
        }

        $data->update($storeData);

        if($resellerPriceUpdated){
            if($data->product_type == 'Product') {
                $data->prodductUsers()->sync($this->mapReseller($data->reseller_price));
            }
        }

        if ($request->product_type == 'Combo' && count($request->product_id) > 0) {
            $itemIdArr = [];
            $mapResellerPrice = 0;
            foreach ($request->product_id as $key => $product) {
                $item = [
                    'id' => $request->item_id[$key],
                    'product_id' => $product,
                    'combo_product_id' => $data->id,
                    'quantity' => $request->quantity[$key]
                ];
                $itemIdArr[] = $data->items()->updateOrCreate(['id' => $item['id']], $item)->id;
                $base = Product::find($product);
                $mapResellerPrice += $base->reseller_price *  $request->quantity[$key];
            }

            $data->prodductUsers()->sync($this->mapReseller($mapResellerPrice));

            //Delete removed item from db...
            $data->otherItems()->whereNotIn('id', $itemIdArr)->delete();
        }


        $request->session()->flash('successMessage', 'Product was successfully updated!');
        return redirect()->route('admin.basic.product-other.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete product');
        $data = Product::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.product-other.index', qArray());
        }

        (new MediaController())->delete('products', $data->image);
        ProductBase::where('base_product_id',$id)->get()->each->delete();
        $data->delete();

        $request->session()->flash('successMessage', 'Product was successfully deleted!');
        return redirect()->route('admin.basic.product-other.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('status product');

        $data = Product::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        $request->session()->flash('successMessage', 'Product status was successfully changed!');
        return redirect()->route('admin.basic.product-other.index', qArray());
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

        return view('admin.basic.product-ecommerce', compact('categories', 'colors', 'sizes', 'items', 'otherImages','data'))->with('create', 1);
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
            return redirect()->route('admin.basic.product-other.index', qArray());
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
        return redirect()->route('admin.basic.product-other.index', qArray());
    }
    

    private function mapReseller($price)
    {
        $users = User::where('role', 'Reseller')->pluck('id', 'id');
        return $users->map(function ($i) use($price) {
            return ['price' => $price];
        });
    }
}
