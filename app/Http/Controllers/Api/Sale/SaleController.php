<?php

namespace App\Http\Controllers\Api\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\SaleCollection;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use Exception;
use Illuminate\Http\Request;

class SaleController extends Controller
{
  /**
     * @authenticated
     * @responseFile responses/sale/sales.json
     */
    public function index()
    {
        try {
            $data = new SaleCollection(Sale::with('items','customer','resellerBusiness','user','shipping','delivery','createdBy','updatedBy')->get());
            return response()->json([
                'status' => true,
                'message' => 'Sales Request Successfully.',
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaleRequest $request)
    {
        try {
            $data = SaleService::store($request);
            return response()->json([
                'status' => true,
                'message' => 'Sale Store Request Successfully.',
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
     * @responseFile responses/sale/single-sale.json
     */
    public function show($id)
    {
        try {
            $data = new SaleResource(Sale::with('items','customer','resellerBusiness','user','shipping','delivery','createdBy','updatedBy')->find($id));
            return response()->json([
                'status' => true,
                'message' => 'Sale Request Successfully.',
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaleRequest $request, $id)
    {
        try {
            $data = SaleService::update($request, $id);
            return response()->json([
                'status' => true,
                'message' => 'Sale Update Request Successfully.',
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            SaleService::delete($id);
            return response()->json([
                'status' => true,
                'message' => 'Sale was successfully deleted.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
