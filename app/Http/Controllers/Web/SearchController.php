<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{


public function index(Request $request)
    {
        $sql = Product::with(['otherInfo', 'productItems' => function ($query) use ($request) {
            if ($request->sort_order == 'high') {
                $query->orderBy('sale_price', 'DESC');
            }
            if ($request->sort_order == 'low') {
                $query->orderBy('sale_price', 'ASC');
            }
        }]);
        $sql->whereHas('otherInfo');
        if ($request->search) {
            $sql->where(function($q) use($request) {
                $q->where('products.name', 'LIKE', '%'. $request->search . '%');
            });
            $sql->orwhereHas('productItems', function($q) use($request) {
                $q->where('barcode', 'LIKE', '%'. $request->search . '%');
                $q->orWhere('sale_price', 'LIKE', '%'. $request->search . '%');
            });
            $sql->orwhereHas('otherInfo.category', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->search . '%');
            });
            $sql->orwhereHas('productItems.color', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->search . '%');
            });
            $sql->orwhereHas('productItems.size', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->search . '%');
            });
        }
        if ($request->sort_order == 'high') {
            $sql->orderBy('id', 'DESC');
        }
        if ($request->sort_order == 'low') {
            $sql->orderBy('id', 'ASC');
        }
        $products = $sql->where('status','Active')->latest()->get();
        $search = $request->search;
        return inertia('search/Show', compact('search','products'));
    }
}
