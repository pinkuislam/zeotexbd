<?php

namespace App\Services;

use App\Models\DeliveryAgent;
use App\Models\DeliveryAgentPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\CodeService;

class DeliveryAgentService
{

    public static function allAgents($user = null)
    {
        $agents = DeliveryAgent::select('id', 'name')->where('status', 'Active')->get();
        return $agents;
    }


    public static function store($request)
    {
        $code = CodeService::generate( DeliveryAgent::class, 'DA', 'code');
        $storeData = [
            'code' => $code,
            'name' => $request->name,
            'type' => $request->type,
            'mobile' => $request->mobile,
            'emergency_mobile' => $request->emergency_mobile,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ];
        $data =  DeliveryAgent::create($storeData);

        return $data;
    }


    public static function update($request, $id)
    {
        $data = DeliveryAgent::findOrFail($id);

        $storeData = [
            'name' => $request->name,
            'type' => $request->type,
            'mobile' => $request->mobile,
            'emergency_mobile' => $request->emergency_mobile,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ];
        $data->update($storeData);

        return $data;
    }

    public static function delete($id)
    {
        $data = DeliveryAgent::findOrFail($id);
        $data->delete();
        return true;
    }
    public static function status($id)
    {
        $data = DeliveryAgent::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => auth()->user()->id]);
        return $data;
    }


    public static function due($deliveryAdgentId, $stockId = null)
    {
        // Opening Due + Sele shipping charge + Received  - (Payment + Adjustment) 

        $delivery = DeliveryAgent::select(DB::raw("((IFNULL(delivery_agents.opening_due, 0) + IFNULL(A.amount, 0) + IFNULL(B.amount, 0)) - IFNULL(C.amount, 0)) AS due"))
        ->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(shipping_charge) AS amount FROM sales WHERE status = 'Delivered' GROUP BY delivery_agent_id) AS A"), function($q) {
            $q->on('delivery_agents.id', '=', 'A.delivery_agent_id');
        })
        ->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(total_amount) AS amount FROM delivery_agent_payments WHERE type = 'Received' GROUP BY delivery_agent_id) AS B"), function($q) {
            $q->on('delivery_agents.id', '=', 'B.delivery_agent_id');
        })
        ->leftJoin(DB::raw("(SELECT delivery_agent_id, SUM(total_amount) AS amount FROM delivery_agent_payments WHERE type != 'Received' GROUP BY delivery_agent_id) AS C"), function($q) {
            $q->on('delivery_agents.id', '=', 'C.delivery_agent_id');
        })
        ->where('id', $deliveryAdgentId)
        ->first();
 
        if ($delivery && $delivery->due) {
            return $delivery->due;
        }
        return 0;
    }
    public static function stockAdjustment($data, $stockId = null)
    {
        if ($stockId) {
            //Delete old adjustment...
            DeliveryAgentPayment::where('type', 'Adjustment')->where('stock_id', $stockId)->delete();
        }

        $code = CodeService::generate(DeliveryAgentPayment::class, '', 'receipt_no');

        $payData = [
            'delivery_agent_id' => $data->delivery_agent_id,
            'stock_id' => $data->id,
            'type' => 'Adjustment',
            'date' => $data->purchase_date,
            'receipt_no' => $code,
            'total_amount' => $data->adjust_amount,
            'total_cost' => 0,
            'total_transaction_amount' => $data->adjust_amount,
            'note' => $data->note,
            'created_by' => Auth::user()->id,
        ];
        DeliveryAgentPayment::create($payData);
    }
}