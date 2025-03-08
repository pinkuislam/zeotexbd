<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Sudip\MediaUploader\Facades\MediaUploader;

class PageController extends Controller {

    public function index(Request $request)
    {
        $sql = Page::with('creator');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('title', 'LIKE', '%'. $request->q . '%');
                $q->orWhereHas('creator', function($r) use($request) {
                    $r->where('name', 'LIKE', '%'. $request->q . '%');
                });
            });
        }
        if ($request->status) {
            $sql->where('status', $request->status);
        }
        $records = $sql->orderBy('id', 'DESC')->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.ecommerce.page.index', compact('records',))->with('list', 1);
    }

    public function create()
    {
        return view('admin.ecommerce.page.create')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'image' => 'nullable|image||mimes:jpg,jpeg,png,webp,gif',
            'details' => 'required',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'details' => $request->details,
            'status' => $request->status,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'created_by' => auth()->user()->id,
        ];
        if ($request->hasFile('image')) {
            $file = MediaUploader::imageUpload($request->image, 'pages', true, null, [600, 600], [80, 80]);
            if ($file) {
                $storeData['image'] = $file['name'];
            }
        }
        Page::create($storeData);

        $request->session()->flash('successMessage', 'Page was successfully added!');
        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Page::with('creator')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }

        return view('admin.ecommerce.page.show', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = Page::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }
        return view('admin.ecommerce.page.edit', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'image' => 'nullable|image||mimes:jpg,jpeg,png,webp,gif',
            'details' => 'required',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Page::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }
        
        $storeData = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'details' => $request->details,
            'status' => $request->status,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'updated_by' => auth()->user()->id,
        ];
        if ($request->image_is_removed == 1) {
            MediaUploader::delete('pages', $data->image, 1);   //Delete Old File
            $storeData['image'] = null;
            $storeData['image_url'] = null;
        }
        if ($request->hasFile('image')) {
            
            MediaUploader::delete('pages', $data->image, 1);   //Delete Old File
            $file = MediaUploader::imageUpload($request->image, 'pages', true, null, [600, 600], [80, 80]);
            if ($file) {
                $storeData['image'] = $file['name'];
            }
        }
        $data->update($storeData);

        $request->session()->flash('successMessage', 'Page was successfully updated!');
        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Page::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }

        $data->delete();
        if ($data->image) {
            MediaUploader::delete('pages', $data->image, true);
        }
        $request->session()->flash('successMessage', 'Page was successfully deleted!');
        return redirect()->action([self::class, 'index'], qArray());
    }
}
