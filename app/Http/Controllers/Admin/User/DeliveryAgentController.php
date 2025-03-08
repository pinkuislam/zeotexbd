<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryAgentRequest;
use App\Models\DeliveryAgent;
use App\Services\CodeService;
use App\Services\DeliveryAgentService;
use Exception;

class DeliveryAgentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list delivery_agent');
        
        $sql = DeliveryAgent::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where('name', 'LIKE', '%'. $request->q . '%')
            ->orWhere('mobile', 'LIKE', '%'. $request->q . '%')
            ->orWhere('emergency_mobile', 'LIKE', '%'. $request->q . '%')
            ->orWhere('type', 'LIKE', '%'. $request->q . '%');
        }

        if ($request->type) {
            $sql->where('type', $request->type);
        }
        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.user.delivery_agent', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add delivery_agent');
        return view('admin.user.delivery_agent')->with('create', 1);
    }

    public function store(DeliveryAgentRequest $request)
    {
        $this->authorize('add delivery_agent');
        try {
            DeliveryAgentService::store($request);
            $request->session()->flash('successMessage', 'Delivery Agent was successfully added!');
            return redirect()->route('admin.user.delivery_agent.create', qArray());
        } catch (Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect()->route('admin.user.delivery_agent.create', qArray());
        }
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show delivery_agent');

        $data = DeliveryAgent::findOrFail($id);
        return view('admin.user.delivery_agent', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit delivery_agent');
        $data = DeliveryAgent::findOrFail($id);
        return view('admin.user.delivery_agent', compact('data'))->with('edit', $id);
    }

    public function update(DeliveryAgentRequest $request, $id)
    {
        $this->authorize('edit delivery_agent');
        try {
            DeliveryAgentService::update($request, $id); 
            $request->session()->flash('successMessage','Delivery Agent was successfully updated!');
            return redirect()->route('admin.user.delivery_agent.index', qArray());
        } catch (Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return back();
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete delivery_agent');
        try {
             DeliveryAgentService::delete($id); 
             $request->session()->flash('successMessage','Delivery Agent was successfully deleted!');
             return redirect()->route('admin.user.delivery_agent.index', qArray());
        } catch (Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return back();
        }
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('edit delivery_agent');
        try {
            DeliveryAgentService::status($id); 
            $request->session()->flash('successMessage', 'Delivery Agent status was successfully changed!');
        return redirect()->route('admin.user.delivery_agent.index', qArray());
        } catch (Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return back();
        }  
    }
    public function due(Request $request)
    {
        $due = DeliveryAgentService::due($request->id);
        return response()->json(['success' => true, 'due' => $due]);
    }

}