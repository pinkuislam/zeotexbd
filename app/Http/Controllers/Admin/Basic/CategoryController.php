<?php

namespace App\Http\Controllers\Admin\Basic;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryExport;
use App\Imports\CategoryImport;
use Illuminate\Database\Eloquent\Builder;
use Sudip\MediaUploader\Facades\MediaUploader;

class CategoryController extends Controller {

    private function records($request): Builder
    {
        $sql = Category::with(['creator', 'parent']);

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q . '%');
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
        
        return $sql;
    }

    public function index(Request $request)
    {
        $this->authorize('list category');

        $records = $this->records($request)->orderBy('id', 'DESC')->paginate($request->limit ?? config('settings.per_page_limit'));;
        // $serial = pagiSerial($records);
        return view('admin.basic.category', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add category');

        $categories = CategoryService::get(false);
        return view('admin.basic.category', compact('categories'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add category');

        $this->validate($request, [
            'parent_id' => 'nullable|integer',
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image||mimes:jpg,jpeg,png,webp,gif',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'created_by' => Auth::user()->id,
        ];

        if ($request->hasFile('image')) {
            $file = MediaUploader::imageUpload($request->image, 'categories', true, null, [600, 600], [80, 80]);
            if ($file) {
                $storeData['image'] = $file['name'];
            }
        }
        $data = Category::create($storeData);
        if ($data) {
            $request->session()->flash('successMessage', 'Category was successfully added.');
        } else {
            $request->session()->flash('errorMessage', 'Category saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show category');

        $data = Category::with(['creator', 'updater', 'parent'])->findOrFail($id);
        return view('admin.basic.category', compact('data'))->with('show', 1);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit category');

        $data = Category::findOrFail($id);

        $categories = CategoryService::get(false, $id);

        return view('admin.basic.category', compact('data', 'categories'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit category');

        $this->validate($request, [
            'parent_id' => 'nullable|integer',
            'name' => 'required|string|max:255|unique:categories,name,'.$id.',id',
            'image' => 'nullable|image||mimes:jpg,jpeg,png,webp,gif',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Category::findOrFail($id);
        $storeData = [
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status,
            'meta_title' => $request->meta_title,
            'meta_keywords' => $request->meta_keywords,
            'meta_description' => $request->meta_description,
            'updated_by' => Auth::user()->id,
        ];


        if ($request->image_is_removed == 1) {
            MediaUploader::delete('categories', $data->image, 1);   //Delete Old File
            $storeData['image'] = null;
            $storeData['image_url'] = null;
        }

        if ($request->hasFile('image')) {
            
            MediaUploader::delete('categories', $data->image, 1);   //Delete Old File
            $file = MediaUploader::imageUpload($request->image, 'categories', true, null, [600, 600], [80, 80]);
            if ($file) {
                $storeData['image'] = $file['name'];
            }
        }
        $data->update($storeData);

        if ($data) {
            $request->session()->flash('successMessage', 'Category was successfully updated.');
        } else {
            $request->session()->flash('errorMessage', 'Category updating failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete category');

        try {
            $data = Category::findOrFail($id);
            $data->delete();
            if ($data->image) {
                MediaUploader::delete('categories', $data->image, true);
            }
            $request->session()->flash('successMessage', 'Category was successfully deleted.');
        } catch (\Exception $e) {
            $request->session()->flash('errorMessage', 'Category deleting failed! Reason: ' . $e->getMessage());
        }
        
        return redirect()->action([self::class, 'index'], qArray());
    }

    public function import(Request $request)
    {
        $this->authorize('add category');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);

        try {
            Excel::import(new CategoryImport, $request->file);

            $request->session()->flash('successMessage', 'Category was successfully imported.');
        } catch (\Exception $e) {
            $request->session()->flash('errorMessage', 'Category importing failed! Reason: ' . $e->getMessage());
        }
        
        return redirect()->action([self::class, 'index'], qArray());
    }

    public function export(Request $request)
    {
        $this->authorize('list category');

        try {
            $records = $this->records($request)->get();

            return Excel::download(new CategoryExport($records), 'categories.xlsx');

            $request->session()->flash('successMessage', 'Category was successfully exported.');
        } catch (\Exception $e) {
            $request->session()->flash('errorMessage', 'Category exporting failed! Reason: ' . $e->getMessage());
        }
        
        return redirect()->action([self::class, 'index'], qArray());
    }
}