<?php

namespace App\Http\Controllers\Admin\Accessory;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessoryRequest;
use App\Imports\AccessoryImport;
use App\Exports\AccessoryExport;
use App\Models\Accessory;
use App\Models\Unit;
use App\Services\CodeService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessoryController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list accessory');
        $sql = Accessory::with(['unit','createdBy'])->orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
                $q->OrWhere('code', 'LIKE', '%'. $request->q.'%');
                $q->OrWhere('unit_price', 'LIKE', '%'. $request->q.'%');
                $q->OrWhere('alert_quantity', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('unit', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
            });
        }


        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $accessories = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.accessory.accessory', compact('accessories'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add accessory');
        $data['units'] = Unit::where('status','Active')->get();
        return view('admin.accessory.accessory', $data)->with('create', 1);
    }

    public function store(AccessoryRequest $request)
    {
        $this->authorize('add accessory');
        $code = CodeService::generate(Accessory::class, 'A', 'code');
        Accessory::create([
            'code' => $code,
            'name' => $request->name,
            'unit_id' => $request->unit_id,
            'unit_price' => $request->unit_price ?? 0.00,
            'alert_quantity' => $request->alert_quantity ?? 0,
            'status' => $request->status,
            'created_by' => auth()->user()->id
        ]);

        $request->session()->flash('successMessage', 'Accessory was successfully added!');
        return redirect()->route('admin.accessory.accessories.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show accessory');
        $data = Accessory::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.accessories.index', qArray());
        }
        return view('admin.accessory.accessory', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit accessory');
        $data = Accessory::find($id);
        $units = Unit::where('status','Active')->get();
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.accessories.index', qArray());
        }
        return view('admin.accessory.accessory', compact('data','units'))->with('edit', $id);
    }

    public function update(AccessoryRequest $request, $id)
    {
        $this->authorize('edit accessory');
        $data = Accessory::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.accessories.index', qArray());
        }

        $data->update([
            'name' => $request->name,
            'unit_id' => $request->unit_id,
            'unit_price' => $request->unit_price ?? 0.00,
            'alert_quantity' => $request->alert_quantity ?? 0,
            'status' => $request->status,
            'updated_by' => auth()->user()->id
        ]);

        $request->session()->flash('successMessage', 'Accessory was successfully updated!');
        return redirect()->route('admin.accessory.accessories.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete accessory');
        $data = Accessory::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.accessory.accessories.index', qArray());
        }
        $data->delete();

        $request->session()->flash('successMessage', 'Accessory was successfully deleted!');
        return redirect()->route('admin.accessory.accessories.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('status accessory');
        $data = Accessory::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => auth()->user()->id]);

        return redirect()->route("admin.accessory.accessories.index")->with("successMessage", "Accessory status was successfully changed!");
    }

    public function import(Request $request)
    {
        $this->authorize('add accessory');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new AccessoryImport, $request->file);

        $request->session()->flash('successMessage', 'Accessory was successfully imported!');
        return redirect()->route('admin.accessory.accessories.index', qArray());
    }

    public function export(Request $request)
    {
        $this->authorize('list accessory');

        return Excel::download(new AccessoryExport, 'accessories-'. time() . '.xlsx');
    }
    public function ledger(Request $request)
    {
        $this->authorize('accessory ledger');

        $sql = Accessory::select(
            'accessories.*',
            DB::raw('IFNULL(A.totalQty, 0) AS totalQty'),
            DB::raw('IFNULL(A.totalUsedQty, 0) AS totalUsedQty'),
            DB::raw('IFNULL(A.stockQty, 0) AS stockQty')
        )->orderBy('name', 'ASC');
        $sql->leftJoin(DB::raw("(
            SELECT accessory_id, 
            SUM(quantity) AS totalQty, 
            SUM(used_quantity) AS totalUsedQty,  
            SUM(quantity - used_quantity) AS stockQty  
            FROM `accessory_items` WHERE type='Purchase' 
            GROUP BY accessory_id) AS A
        "), function($q) {
            $q->on('A.accessory_id', '=', 'accessories.id');
        });

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('accessories.name', 'LIKE', $request->q.'%');
            });
        }
        if($request->accessory_id > 0){
            $sql->where('accessories.id',$request->accessory_id);
        }
        $reports = $sql->get();
        $accessories = Accessory::select('id','name')->where('status','Active')->get();
        if ($request['action'] == 'print') {
            $title = 'Accessory Ladger';
            return view('admin.report.print.accessory-list', compact('reports','accessories','title'));
        }
        return view('admin.accessory.accessory-list', compact('reports','accessories'));
    }
    public function ledgerDetails(Request $request)
    {
        $accessories = Accessory::select('id','name')->where('status','Active')->get();
        if ($request->accessory_id == null) {
            return view('admin.accessory.accessory-list-details', compact('accessories'));
        }
        $accessory = Accessory::find($request->accessory_id);
        $asOnDate = '';
        $from = '1970-01-01';
        $to = date('Y-m-d');
        if ($request->from) {
            $asOnDate .= "AND DATE(created_at) >= '".dbDateFormat($request->from)."'";
            $from = $request->from;
        }
        if ($request->to) {
            $asOnDate .= "AND DATE(created_at) <= '".dbDateFormat($request->to)."'";
            $to = $request->to;
        }
        $query1 = "SELECT 'Purchase' AS type, 'admin.accessory.purchase.show' AS route, flagable_id AS rowId, created_at, quantity,(used_quantity) as usedQuantity,unit_price as amount FROM accessory_items WHERE accessory_id = $request->accessory_id AND type = 'Purchase' $asOnDate";
        $query2 = "SELECT 'Consume' AS type, 'admin.accessory.consume.show' AS route, flagable_id AS rowId, created_at, quantity,'0' as usedQuantity,unit_price as amount FROM accessory_items WHERE accessory_id = $request->accessory_id AND type = 'Consume' $asOnDate";

        $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2) S ORDER BY S.`created_at` ASC");
        
        if ($request['action'] == 'print') {
            $title = 'Accessory Ladger Details';
            return view('admin.report.print.accessory-list-details', compact('reports','accessories','accessory','title'));
        }
        return view('admin.accessory.accessory-list-details', compact('reports','accessories','accessory'));
    }
}