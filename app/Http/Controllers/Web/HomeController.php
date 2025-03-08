<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Highlight;
use App\Models\Page;

class HomeController extends Controller
{
    public function __invoke()
    {

        $data['sliders'] = Slider::where('status', 'Active')->get();
        $data['highlights'] = Highlight::where('status', 'Active')->latest()->take(4)->get();
        $data['new_arrival_products'] = Product::with('otherInfo', 'productItems')->whereHas('otherInfo', function($q){
            $q->where('is_new_arrival', 'Yes');
        })->latest()->limit(30)->get();
        $data['best_selling_products'] = Product::with('otherInfo', 'productItems')->whereHas('otherInfo')->latest()->limit(30)->get();
        return inertia('Home', $data);
    }

    public function page($slug){
        $page = Page::where('slug', $slug)->first();
        return inertia('',compact('page'));
    }

}
