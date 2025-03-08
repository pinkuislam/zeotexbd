<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Oshnisoft\HRM\Models\AssetRequisition;

class AssetRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sql = AssetRequisition::with(['employee', 'updatedBy'])->orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('item', 'LIKE', $request->q . '%');
                $q->orWhere('note', 'LIKE', $request->q . '%');
                $q->orWhere('feedback', 'LIKE', $request->q . '%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->paginate($request->limit ?? 15);

        return view('hrm::asset-requisition', compact('records'))->with('list', 1);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\AssetRequisition $unit
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = AssetRequisition::findOrFail($id);
        return view('hrm::asset-requisition', compact('data'))->with('show', $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AssetRequisition $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:Approved,Canceled'
        ]);

        $data = AssetRequisition::findOrFail($id);

        $storeData = [
            'status' => $request->status,
            'feedback' => $request->feedback,
            'updated_by' => Auth::user()->id,
        ];
        $data->update($storeData);

        $request->session()->flash('successMessage', 'Asset Requisition was successfully updated!');
        return redirect()->route('oshnisoft-hrm.asset-requisition.index');
    }
}
