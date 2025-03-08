<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DyeingAgent;
use App\Services\CodeService;
use App\Services\DyeingAgentService;

class DyeingAgentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list dyeing-agent');
        
        $sql = DyeingAgent::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where('name', 'LIKE', '%'. $request->q . '%')
            ->orWhere('code', 'LIKE', '%'. $request->q . '%')
            ->orWhere('contact_person', 'LIKE', '%'. $request->q . '%')
            ->orWhere('contact_no', 'LIKE', '%'. $request->q . '%')
            ->orWhere('address', 'LIKE', '%'. $request->q . '%')
            ->orWhere('email', 'LIKE', '%'. $request->q . '%');
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.user.dyeing-agent', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add dyeing-agent');
        return view('admin.user.dyeing-agent')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add dyeing-agent');
        $rules = [
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255|unique:dyeing_agents,contact_no',
            'status' => 'required|in:Active,Deactivated',
        ];
        $rules['email'] = 'nullable|string|max:255|unique:dyeing_agents,email';
        
        $this->validate($request, $rules);
        
        $code = CodeService::generateUserCode('DyeingAgent', DyeingAgent::class, 'code');
        
        $storeData = [
            'code' => $code,
            'name' => $request->name,
            'contact_no' => $request->contact_no,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'address' => $request->address,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ];
        
        DyeingAgent::create($storeData);

        $request->session()->flash('successMessage', 'Dyeing Agent was successfully added!');
        return redirect()->route('admin.user.dyeing-agent.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('list dyeing-agent');

        $data = DyeingAgent::findOrFail($id);
        return view('admin.user.dyeing-agent', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit dyeing-agent');
        $data = DyeingAgent::findOrFail($id);
        return view('admin.user.dyeing-agent', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit dyeing-agent');

        $rules = [
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255|unique:dyeing_agents,contact_no,' . $id . ',id',
            'status' => 'required|in:Active,Deactivated',
        ];
        $rules['email'] = 'nullable|string|max:255|unique:dyeing_agents,email,' . $id . ',id';

        $this->validate($request, $rules);

        $data = DyeingAgent::findOrFail($id);

        $storeData = [
            'name' => $request->name,
            'contact_no' => $request->contact_no,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'address' => $request->address,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ];
        $data->update($storeData);

        $request->session()->flash('successMessage','Dyeing Agent was successfully updated!');
        return redirect()->route('admin.user.dyeing-agent.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        
        $this->authorize('delete dyeing-agent');
        $data = DyeingAgent::findOrFail($id);
        $data->delete();
        
        $request->session()->flash('successMessage','Dyeing Agent was successfully deleted!');
        return redirect()->route('admin.user.dyeing-agent.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('edit dyeing-agent');

        $data = DyeingAgent::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => auth()->user()->id]);

        $request->session()->flash('successMessage', 'Dyeing Agent status was successfully changed!');
        return redirect()->route('admin.user.dyeing-agent.index', qArray());
    }
    public function due(Request $request)
    {
        $due = DyeingAgentService::due($request->id);
        return response()->json(['success' => true, 'due' => $due]);
    }
}