<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Support\Facades\Auth;
use Sudip\MediaUploader\Facades\MediaUploader;

class SliderController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list slider');

        $sql = Slider::with('creator');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('title', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('link', 'LIKE', '%'. $request->q . '%');
                $q->orWhereHas('creator', function($r) use($request) {
                    $r->where('name', 'LIKE', '%'. $request->q . '%');
                });
            });
        }

        if ($request->from) {
            $sql->where('created_at', '>=', $request->from);
        }

        if ($request->to) {
            $sql->where('created_at', '<=', $request->to);
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->orderBy('id', 'DESC')->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.ecommerce.slider.index', compact('records'));
    }

    public function create()
    {
        $this->authorize('add slider');
        return view('admin.ecommerce.slider.create');
    }

    public function store(Request $request)
    {
        $this->authorize('add slider');

        $this->validate($request, [
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'image' => 'required|image||mimes:jpg,jpeg,png,webp,gif',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'title' => $request->title,
            'link' => $request->link,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];

        if ($request->hasFile('image')) {
            $file = MediaUploader::anyUpload($request->image, 'sliders', null);
            if ($file) {
                $storeData['image'] = $file['name'];
            }
        }

        $data = Slider::create($storeData);
        if ($data) {
            $request->session()->flash('successMessage', 'Slider was successfully added.');
        } else {
            $request->session()->flash('errorMessage', 'Slider saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show slider');

        $data = Slider::with(['creator', 'updater'])->findOrFail($id);
        return view('admin.ecommerce.slider.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit slider');

        $data = Slider::findOrFail($id);
        return view('admin.ecommerce.slider.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit slider');

        $this->validate($request, [
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image||mimes:jpg,jpeg,png,webp,gif',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Slider::findOrFail($id);
        $storeData = [
            'title' => $request->title,
            'link' => $request->link,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        if ($request->image_is_removed == 1) {
            MediaUploader::delete('sliders', $data->image, 1);   //Delete Old File
            $storeData['image'] = null;
        }

        if ($request->hasFile('image')) {
            MediaUploader::delete('sliders', $data->image, 1);   //Delete Old File
            $file = MediaUploader::anyUpload($request->image, 'sliders', null);
            if ($file) {
                $storeData['image'] = $file['name'];
            }
        }

        $data->update($storeData);

        if ($data) {
            $request->session()->flash('successMessage', 'Slider was successfully updated.');
        } else {
            $request->session()->flash('errorMessage', 'Slider updating failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete slider');

        try {
            $data = Slider::findOrFail($id);
            $data->delete();

            if ($data->image) {
                MediaUploader::delete('sliders', $data->image, true);
            }

            $request->session()->flash('successMessage', 'Slider was successfully deleted.');
        } catch (\Exception $e) {
            $request->session()->flash('errorMessage', 'Slider deleting failed! Reason: ' . $e->getMessage());
        }
        
        return redirect()->action([self::class, 'index'], qArray());
    }
}
