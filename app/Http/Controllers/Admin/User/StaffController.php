<?php

namespace App\Http\Controllers\Admin\User;

use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    use UserTrait;
    protected $role = 'Staff';

    public function index(Request $request)
    {
        $this->authorize('list staff');
        $records = $this->users($this->role, $request)->paginate($request->limit ?? config('settings.per_page_limit'));
        return view('admin.user.staff', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add staff');
        $roles = Role::get();

        return view('admin.user.staff', compact('roles'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add staff');
        $this->validate($request, $this->userRules($this->role, $request, null));

        $this->userCreate($request, $request->access_role);

        $request->session()->flash('successMessage', $this->role . ' was successfully added!');
        return redirect()->route('admin.user.staff.create', qArray());
    }

    public function show($id)
    {
        $this->authorize('show staff');
        $data = $this->user($this->role)->findOrFail($id);
        return view('admin.user.staff', compact('data'))->with('show', $id);
    }

    public function edit($id)
    {
        $this->authorize('edit staff');
        $data = $this->user($this->role)->findOrFail($id);
        $roles = Role::get();
        return view('admin.user.staff', compact('data', 'roles'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit staff');
        $this->validate($request, $this->userRules($this->role, $request, $id));

        $this->userUpdate($this->role, $request, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully updated!');
        return redirect()->route('admin.user.staff.index', qArray());
    }

    public function destroy(Request $request, $id)
    {

        $this->authorize('delete staff');
        $this->userDelete($this->role, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully deleted!');
        return redirect()->route('admin.user.staff.index', qArray());
    }
}