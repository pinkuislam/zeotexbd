<?php

namespace App\Http\Controllers\Admin\Dyeing;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendDyeingRequest;
use App\Models\DyeingAgent;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\SendDyeing;
use App\Models\ProductOut;
use App\Models\ProductUse;
use App\Models\ReceiveDyeing;
use App\Services\CodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SendDyeingController extends Controller
{

    protected $type = "Dyeing";

    public function index(Request $request)
    {
        $this->authorize('list send-dyeing');
        $sql = SendDyeing::with('dyeingAgent', 'greyItems', 'greyItems.product')->orderBy('created_at', 'DESC');

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
            $sql->where('send-dyeings.date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('send-dyeings.date', '<=', dbDateFormat($request->to));
        }
        $data = $sql->paginate($request->limit ?? config('settings.per_page_limit'));
        
        $dyeingAgents = DyeingAgent::where('status', 'Active')->latest()->get(['id', 'name']);

        return view('admin.dyeing.send', compact('data', 'dyeingAgents'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add send-dyeing');
        $product = Product::with(['unit'])->whereIn('product_type', ['Grey'])->where('status', 'Active')->firstOrFail();
        $stock = $product->getStock();
        $dyeingAgents = DyeingAgent::where('status', 'Active')->latest()->get(['id', 'name']);
        return view('admin.dyeing.send', compact('product', 'stock', 'dyeingAgents'))->with('create', 1);
    }

    public function store(SendDyeingRequest $request)
    {
        $this->authorize('add send-dyeing');
        DB::beginTransaction();
        try {
            $code = CodeService::generate(SendDyeing::class, 'SD', 'code');

            $storeData = [
                'code' => $code,
                'date' => dbDateFormat($request->date),
                'dyeing_agent_id' => $request->dyeing_agent_id,
                'note' => $request->note,
                'created_by' => auth()->user()->id
            ];

            $data = SendDyeing::create($storeData);

            $itemDataProd = [
                'type' => $this->type,
                'flagable_id' => $data->id,
                'flagable_type' => SendDyeing::class,
                'product_id' => $request->product_id,
                'unit_id' => $request->unit_id,
                'color_id' => null,
                'quantity' => $request->quantity,
                'unit_price' => 0,
                'total_price' => 0,
                'created_at' => now(),
            ];
            $outproduct = ProductOut::create($itemDataProd);
            $inproducts =  ProductIn::where('product_id', $request->product_id)->where('status', 0)->get();
            $rawQty = $request->quantity;
            $avgUnitPrice = 0;
            foreach ($inproducts as $inproduct) {
                if ($rawQty > 0) {
                    if (($inproduct->quantity - $inproduct->used_quantity) < $rawQty) {
                        $rawQty -= ($inproduct->quantity - $inproduct->used_quantity);
                        ProductUse::create([
                            'product_in_id' => $inproduct->id,
                            'product_out_id' => $outproduct->id,
                            'quantity' => $inproduct->quantity - $inproduct->used_quantity,
                        ]);
                        $avgUnitPrice += ($inproduct->quantity - $inproduct->used_quantity) * $inproduct->unit_price;
                        $inproduct->update([
                            'used_quantity' => $inproduct->quantity,
                            'status' => 1
                        ]);
                    } else if (($inproduct->quantity - $inproduct->used_quantity) > $rawQty) {
                        $inproduct->update([
                            'used_quantity' => $rawQty + $inproduct->used_quantity,
                            'status' => 0
                        ]);
                        ProductUse::create([
                            'product_in_id' => $inproduct->id,
                            'product_out_id' => $outproduct->id,
                            'quantity' => $rawQty,
                        ]);
                        $avgUnitPrice += $rawQty * $inproduct->unit_price;
                        $rawQty = 0;
                    } else {
                        $inproduct->update([
                            'used_quantity' => $inproduct->quantity,
                            'status' => 1
                        ]);
                        ProductUse::create([
                            'product_in_id' => $inproduct->id,
                            'product_out_id' => $outproduct->id,
                            'quantity' => $rawQty,
                        ]);
                        $avgUnitPrice += $rawQty * $inproduct->unit_price;
                        $rawQty = 0;
                    }
                }
            }

            $outproduct->update(['unit_price' => $avgUnitPrice / $request->quantity, 'total_price' => $avgUnitPrice]);

            DB::commit();
            $request->session()->flash('successMessage', 'Send To Dyeing was successfully added!');
            return redirect()->route('admin.send-dyeing.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.send-dyeing.index');
        }
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show send-dyeing');
        $data = SendDyeing::with('dyeingAgent', 'greyItems', 'greyItems.product')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.send-dyeing.index', qArray());
        }

        return view('admin.dyeing.send', compact('data'))->with('show', $id);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete send-dyeing');
        $data = SendDyeing::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.send-dyeing.index', qArray());
        }

        DB::beginTransaction();
        try {

            $productionUsed = ProductUse::where('product_outs.flagable_id', $id)->where('product_outs.flagable_type', SendDyeing::class)
                ->select('product_uses.*')
                ->join('product_outs', 'product_uses.product_out_id', '=', 'product_outs.id')
                ->get();
            foreach ($productionUsed as $value) {
                $info = ProductIn::find($value->product_in_id);
                ProductIn::where('id', $value->product_in_id)->update(['used_quantity' => ($info->used_quantity - $value->quantity), 'status' => 0]);
            }

            $usesIds = $productionUsed->pluck('id');
            ProductUse::whereIn('id', $usesIds)->delete();
            ProductOut::where('flagable_id', $data->id)->where('flagable_type', SendDyeing::class)->delete();
            ProductIn::where('flagable_id', $data->id)->where('flagable_type', SendDyeing::class)->delete();
            $data->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Send was not deleted! ' . $e);
            return redirect()->route('admin.send-dyeing.index', qArray());
        }

        $request->session()->flash('successMessage', 'Send was successfully deleted!');
        return redirect()->route('admin.send-dyeing.index', qArray());
    }

    public function getAgentStock(Request $request)
    {
        $send = ProductOut::select(
            'product_outs.*', 
            'send_dyeings.id', 
            'send_dyeings.dyeing_agent_id', 
            DB::raw('ifnull(sum(product_outs.quantity), 0) as send')
        )
        ->leftJoin('send_dyeings', function ($q) {            
            $q->on('product_outs.flagable_id', '=', 'send_dyeings.id');                      
        })
        ->where('product_outs.type', 'Dyeing')
        ->where('send_dyeings.dyeing_agent_id', $request->agent_id)
        ->groupBy('product_outs.product_id')
        ->get();
        $receive = ReceiveDyeing::select(DB::raw('ifnull(sum(grey_fabric_consume), 0) as consumed'))->where('receive_dyeings.dyeing_agent_id', $request->agent_id)->get();
        return response()->json(['success' => true, 'stock' => ($send[0]->send - $receive[0]->consumed)]);
    }

    
}
