<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Highlight;
use Sudip\MediaUploader\Facades\MediaUploader;

class HighlightController extends Controller {

    public function index(Request $request)
    {
        $sql = Highlight::with('creator');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('title', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('link', 'LIKE', '%'. $request->q . '%');
                $q->orWhereHas('creator', function($r) use($request) {
                    $r->where('name', 'LIKE', '%'. $request->q . '%');
                });
            });
        }
        if ($request->status) {
            $sql->where('status', $request->status);
        }
        $records = $sql->orderBy('id', 'DESC')->paginate($request->limit ?? config('settings.per_highlight_limit'));

        return view('admin.ecommerce.highlight.index', compact('records',))->with('list', 1);
    }

    public function create()
    {
        return view('admin.ecommerce.highlight.create')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'link' => 'required|max:255',
            'image' => 'required|image||mimes:jpg,jpeg,png,webp,gif',
            'is_new_tab' => 'required|in:Yes,No',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'title' => $request->title,
            'link' => $request->link,
            'status' => $request->status,
            'is_new_tab' => $request->is_new_tab,
            'created_by' => auth()->user()->id,
        ];
        if ($request->hasFile('image')) {
            $file = MediaUploader::imageUpload($request->image, 'highlights', true, null, [600, 600], [80, 80]);
            if ($file) {
                $storeData['image'] = $file['name'];
            }
        }
        Highlight::create($storeData);

        $request->session()->flash('successMessage', 'Highlight was successfully added!');
        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Highlight::with('creator')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }

        return view('admin.ecommerce.highlight.show', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = Highlight::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }
        return view('admin.ecommerce.highlight.edit', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'link' => 'required|max:255',
            'image' => 'nullable|image||mimes:jpg,jpeg,png,webp,gif',
            'is_new_tab' => 'required|in:Yes,No',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Highlight::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }
        
        $storeData = [
            'title' => $request->title,
            'link' => $request->link,
            'status' => $request->status,
            'is_new_tab' => $request->is_new_tab,
            'updated_by' => auth()->user()->id,
        ];
        if ($request->image_is_removed == 1) {
            MediaUploader::delete('highlights', $data->image, 1);   //Delete Old File
            $storeData['image'] = null;
            $storeData['image_url'] = null;
        }
        if ($request->hasFile('image')) {
            
            MediaUploader::delete('highlights', $data->image, 1);   //Delete Old File
            $file = MediaUploader::imageUpload($request->image, 'highlights', true, null, [600, 600], [80, 80]);
            if ($file) {
                $storeData['image'] = $file['name'];
            }
        }
        $data->update($storeData);

        $request->session()->flash('successMessage', 'Highlight was successfully updated!');
        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {
        $data = Highlight::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }

        $data->delete();
        if ($data->image) {
            MediaUploader::delete('highlights', $data->image, true);
        }
        $request->session()->flash('successMessage', 'Highlight was successfully deleted!');
        return redirect()->action([self::class, 'index'], qArray());
    }
}