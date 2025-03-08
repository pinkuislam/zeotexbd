<?php

namespace App\Http\Controllers\Admin\Dyeing;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionRequest;
use App\Http\Requests\ReceiveDyeingRequest;
use App\Models\ReceiveDyeing;
use App\Models\ProductIn;
use App\Models\Product;
use App\Models\DyeingAgent;
use App\Services\CodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiveDyeingController extends Controller
{

    protected $type = "Dyeing";

    public function index(Request $request)
    {
        $this->authorize('list receive-dyeing');
        $sql = ReceiveDyeing::with('items', 'dyeingAgent', 'items.product')->orderBy('created_at', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('note', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('date', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->dyeing_agent_id) {
            $sql->where('dyeing_agent_id', $request->dyeing_agent_id);
        }

        if ($request->from) {
            $sql->where('receive_dyeings.date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('receive_dyeings.date', '<=', dbDateFormat($request->to));
        }
        $data = $sql->paginate($request->limit ?? config('settings.per_page_limit'));
        $dyeingAgents = DyeingAgent::where('status', 'Active')->latest()->get(['id', 'name']);

        return view('admin.dyeing.receive', compact('data', 'dyeingAgents'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add receive-dyeing');
        $products = Product::with(['unit'])->whereIn('product_type', ['Fabric'])->where('status', 'Active')->get();
        $dyeingAgents = DyeingAgent::where('status', 'Active')->latest()->get(['id', 'name']);
        $grey = Product::with(['unit'])->whereIn('product_type', ['Grey'])->where('status', 'Active')->firstOrFail();
        return view('admin.dyeing.receive', compact('products', 'dyeingAgents', 'grey'))->with('create', 1);
    }

    public function store(ReceiveDyeingRequest $request)
    {
        $this->authorize('add receive-dyeing');
        DB::beginTransaction();
        try {

            $code = CodeService::generate(ReceiveDyeing::class, 'RD', 'code');

            $storeData = [
                'code' => $code,
                'date' => dbDateFormat($request->date),
                'dyeing_agent_id' => $request->dyeing_agent_id,
                'note' => $request->note,
                'unit_price' => $request->unit_price,
                'total_cost' => $request->total_cost,
                'grey_fabric_consume' => $request->grey_fabric_consume,
                'created_by' => auth()->user()->id
            ];

            $data = ReceiveDyeing::create($storeData);

            $itemData = [
                'type' => $this->type,
                'flagable_id' => $data->id,
                'flagable_type' => ReceiveDyeing::class,
                'product_id' => $request->product_id,
                'unit_id' => $request->unit_id,
                'color_id' => null,
                'quantity' => $request->quantity ?? 0,
                'unit_price' => $data->unit_price ?? 0,
                'total_price' => $data->total_cost ?? 0,
                'used_quantity' => 0,
                'status' => 0,
                'created_at' => now(),
            ];
            ProductIn::create($itemData);
            DB::commit();
            $request->session()->flash('successMessage', 'Received from Dyeing was successfully added!');
            return redirect()->route('admin.receive-dyeing.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.receive-dyeing.index');
        }
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show receive-dyeing');
        $data = ReceiveDyeing::with('items', 'dyeingAgent', 'items.product')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.receive-dyeing.index', qArray());
        }

        return view('admin.dyeing.receive', compact('data'))->with('show', $id);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete receive-dyeing');
        $data = ReceiveDyeing::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.receive-dyeing.index', qArray());
        }

        DB::beginTransaction();
        try {

            $data = ReceiveDyeing::with('items')->find($id);

            if (empty($data)) {
                $request->session()->flash('errorMessage', 'Data not found!');
                return redirect()->route('admin.receive-dyeing.index', qArray());
            }

            foreach($data->items as $item){
                if($item->used_quantity > 0){
                    $request->session()->flash('errorMessage', 'This Received items already consumed !!');
                    return redirect()->route('admin.receive-dyeing.index', qArray());
                }
                $item->delete();
            }
            $data->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Received was not deleted! ' . $e);
            return redirect()->route('admin.receive-dyeing.index', qArray());
        }

        $request->session()->flash('successMessage', 'Received was successfully deleted!');
        return redirect()->route('admin.receive-dyeing.index', qArray());
    }
    
}
