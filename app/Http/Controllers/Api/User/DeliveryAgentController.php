<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryAgentRequest;
use App\Http\Resources\DeliveryAgentCollection;
use App\Http\Resources\DeliveryAgentResource;
use App\Models\DeliveryAgent;
use App\Services\DeliveryAgentService;
use Exception;
use Illuminate\Http\Request;

class DeliveryAgentController extends Controller
{
     /**
     * @authenticated
     * @responseFile responses/user/delivery-agents.json
     */
    public function index()
    {
        try {
            $data = new DeliveryAgentCollection(DeliveryAgent::where('status','Active')->latest()->get());
            return response()->json([
                'status' => true,
                'message' => 'Delivery Agents Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryAgentRequest $request)
    {
        try {
            $data = DeliveryAgentService::store($request);
            return response()->json([
                'status' => true,
                'message' => 'Delivery Agent Store Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

     /**
     * @authenticated
     * @responseFile responses/user/single-delivery-agent.json
     */
    public function show($id)
    {
        try {
            $data = new DeliveryAgentResource(DeliveryAgent::find($id));
            return response()->json([
                'status' => true,
                'message' => 'Delivery Agent Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryAgentRequest $request, $id)
    {
        try {
            $data = DeliveryAgentService::update($request, $id);
            return response()->json([
                'status' => true,
                'message' => 'Delivery Agent Update Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
       /**
     * @authenticated
     * @Method DELETE
     * @response
     *  {
      *  "status": true,
      *  "message": "Customer was successfully deleted."
     *   }
     */
    public function destroy($id)
    {
        try {
            DeliveryAgentService::delete($id);
            return response()->json([
                'status' => true,
                'message' => 'Delivery Agent was successfully deleted.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * @authenticated
     * @Method PUT
     * @responseFile responses/user/delivery-agent-status.json
     */
    public function statusChange($id)
    {
        try {
            $data = DeliveryAgentService::status($id);
            return response()->json([
                'status' => true,
                'message' => 'Delivery Agent status Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
