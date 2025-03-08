<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\ProductOtherInfo;
use App\Models\ProductReview;
use App\Models\Page;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $otherinfo = ProductOtherInfo::where('slug', $id)->first();
        $product = Product::with([
            'otherInfo',
            'otherInfo.category', 
            'otherInfo.category.parent', 
            'otherInfo.category.parent.parent', 
            'productItems', 
            'otherImages', 
            'productItems.color',
            'productItems.size', 
            'reviews'
        ])->findOrFail($otherinfo->product_id);
        $relatedProduct = ProductOtherInfo::with([
            'product',
            'product.productItems',
            'product.otherInfo'
            ])->where('category_id', $product->otherInfo->category_id)->where('product_id','!=',$product->id)->get();
        $return_policy = Page::where('slug', 'return-policy')->where('status','Active')->first();
        return inertia('products/Show', compact('product', 'relatedProduct','return_policy'));
    }


    public function review(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'phone' => 'nullable|max:11',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'nullable|string|min:10',
        ]);

        $data = Product::findOrFail($id);
        $storeData = [
            'product_id' => $data->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'rating' => $request->rating,
            'message' => $request->message,
        ];
        $review = ProductReview::create($storeData);
        if ($review) {
            $request->session()->flash('successMessage', 'Your review was successfully added. Thank you for your review');
        } else {
            $request->session()->flash('errorMessage', 'Review adding failed!');
        }
        return redirect()->back();
    }
    public function variantProduct(Request $request)
    {
        try {
            $sql = ProductItem::where('product_id', $request->id);
            if ($request->color != 'NaN') {
                $sql->where('color_id', $request->color);
            } 
            if ($request->size != 'NaN') {
                $sql->where('size_id', $request->size);
            }
            $data = $sql->first();
            return response()->json([
                'status' => true,
                'message' => 'Variant Product Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
