<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductOtherInfo;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __invoke(Request $request, $slug)
    {
        $category = Category::with('parent', 'parent.parent', 'childs', 'childs.childs')->where('slug', $slug)->first();;

        $category_ids = [];

        if ($category->parent_id) {
            if (count($category->childs) > 0) {
                foreach ($category->childs as $subcategory) {
                    $category_ids[] = $subcategory->id;
                }
            } else {
                $category_ids[] = $category->id;
            }
        } else {
            foreach ($category->childs as $subcategory) {
                foreach ($subcategory->childs as $childcategory) {
                    $category_ids[] = $childcategory->id;
                }
            }
        }

        $sql = ProductOtherInfo::with([
                'product',
                'product.productItems' => function ($query) use ($request) {
                    if ($request->sort_order == 'high') {
                        $query->orderBy('sale_price', 'DESC');
                    }
                    if ($request->sort_order == 'low') {
                        $query->orderBy('sale_price', 'ASC');
                    }
                },
                'product.otherInfo'
            ])
            ->whereIn('category_id', $category_ids);
            if ($request->sort_order == 'high') {
                $sql->orderBy('id', 'DESC');
            }
            if ($request->sort_order == 'low') {
                $sql->orderBy('id', 'ASC');
            }
            $products = $sql->get();

        return inertia('category/Show', compact('category', 'products'));
    }
}
