<?php

namespace App\Http\Controllers\Admin\User;

use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ResellerBusinessService;

class ResellerBusinessController extends Controller
{
    use UserTrait;
    protected $role = 'Reseller Business';

    public function index(Request $request)
    {
        $this->authorize('list reseller_business');
        $records = $this->users($this->role, $request)->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.user.reseller_business', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add reseller_business');
        $roles = Role::get();

        return view('admin.user.reseller_business', compact('roles'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add reseller_business');
        $this->validate($request, $this->userRules($this->role, $request, null));

        $this->userCreate($request, $request->access_role);

        $request->session()->flash('successMessage', $this->role . ' was successfully added!');
        return redirect()->route('admin.user.reseller_business.create', qArray());
    }

    public function show($id)
    {
        $this->authorize('show reseller_business');
        $data = $this->user($this->role)->findOrFail($id);
        return view('admin.user.reseller_business', compact('data'))->with('show', $id);
    }

    public function edit($id)
    {
        $this->authorize('edit reseller_business');
        $data = $this->user($this->role)->findOrFail($id);
        $roles = Role::get();
        return view('admin.user.reseller_business', compact('data', 'roles'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit reseller_business');
        $this->validate($request, $this->userRules($this->role, $request, $id));

        $this->userUpdate($this->role, $request, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully updated!');
        return redirect()->route('admin.user.reseller_business.index', qArray());
    }

    public function destroy(Request $request, $id)
    {

        $this->authorize('delete reseller_business');
        $this->userDelete($this->role, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully deleted!');
        return redirect()->route('admin.user.reseller_business.index', qArray());
    }
    public function getResellerBusiness()
    {
        $res = User::where('role', $this->role)->where('status','Active')->orderBy('id', 'DESC')->get();
        if ($res) {
            return response()->json(['success' => true, 'data' => $res]);
        }
        return response()->json(['success' => false]);
    }
    public function due(Request $request)
    {
        $due = ResellerBusinessService::due($request->id);
        return response()->json(['success' => true, 'due' => $due]);
    }
}