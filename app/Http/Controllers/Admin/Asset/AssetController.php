<?php

namespace App\Http\Controllers\Admin\Asset;

use App\Exports\AssetExport;
use App\Http\Controllers\Controller;
use App\Imports\AssetImport;
use App\Models\Asset;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list asset');
        $sql = Asset::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $assets = $sql->get();

        return view('admin.asset.asset', compact('assets'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add asset');
        return view('admin.asset.asset')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add asset');
        $this->validate($request, [
            'name' => 'required|max:255|unique:assets,name',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => auth()->user()->id
        ];
        Asset::create($storeData);

        $request->session()->flash('successMessage', 'Asset was successfully added!');
        return redirect()->route('admin.asset.assets.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show asset');
        $data = Asset::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.asset.assetindex', qArray());
        }
        return view('admin.asset.asset', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit asset');
        $data = Asset::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.asset.assets.index', qArray());
        }
        return view('admin.asset.asset', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit asset');
        $this->validate($request, [
            'name' => 'required|max:255|unique:assets,name,'.$id.',id',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $data = Asset::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.asset.assets.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => auth()->user()->id
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Asset was successfully updated!');
        return redirect()->route('admin.asset.assets.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete asset');
        $data = Asset::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.asset.assets.index', qArray());
        }
        $data->delete();

        $request->session()->flash('successMessage', 'Asset was successfully deleted!');
        return redirect()->route('admin.asset.assets.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('status asset');
        $data = Asset::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => auth()->user()->id]);

        return redirect()->route("admin.asset.assets.index")->with("successMessage", "Asset status was successfully changed!");
    }

    public function import(Request $request)
    {
        $this->authorize('add asset');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new AssetImport, $request->file);

        $request->session()->flash('successMessage', 'Asset was successfully imported!');
        return redirect()->route('admin.asset.assets.index', qArray());
    }

    public function export(Request $request)
    {
        $this->authorize('list asset');

        return Excel::download(new AssetExport, 'asset-'. time() . '.xlsx');
    }
    public function ledger(Request $request)
    {
        $this->authorize('asset ledger');

        $sql = Asset::select(
            'assets.*',
            DB::raw('IFNULL(A.totalQty, 0) AS totalQty'),
            DB::raw('IFNULL(A.totalAmount, 0) AS totalAmount') 
        )->orderBy('name', 'ASC');
        $sql->leftJoin(DB::raw("(SELECT asset_id, SUM(quantity) AS totalQty, SUM(total_amount) AS totalAmount  FROM `asset_items` GROUP BY asset_id) AS A"), function($q) {
            $q->on('A.asset_id', '=', 'assets.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('assets.name', 'LIKE', $request->q.'%');
            });
        }
        if($request->asset_id > 0){
            $sql->where('assets.id',$request->asset_id);
        }
        $reports = $sql->get();
        $assets = Asset::where('status','Active')->get();
        
        return view('admin.asset.asset-list', compact('reports','assets'));
    }
}