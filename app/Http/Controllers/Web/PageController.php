<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function index($slug){
        $page = Page::where('slug', $slug)->first();
        return inertia('page/Index',compact('page'));
    }

}
