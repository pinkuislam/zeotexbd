<?php

namespace App\Http\Controllers\Admin\User;

use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    use UserTrait;
    protected $role = 'Admin';

    public function index(Request $request)
    {
        $this->authorize('list admin');
        $records = $this->users($this->role, $request)->paginate($request->limit ?? config('settings.per_page_limit'));
        return view('admin.user.admin', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add admin');
        $roles = Role::get();

        return view('admin.user.admin', compact('roles'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add admin');
        $this->validate($request, $this->userRules($this->role, $request, null));

        $this->userCreate($request, $this->role);

        $request->session()->flash('successMessage', $this->role . ' was successfully added!');
        return redirect()->route('admin.user.admin.create', qArray());
    }

    public function show($id)
    {
        $this->authorize('show admin');
        $data = $this->user($this->role)->findOrFail($id);
        return view('admin.user.admin', compact('data'))->with('show', $id);
    }

    public function edit($id)
    {
        $this->authorize('edit admin');
        $data = $this->user($this->role)->findOrFail($id);
        $roles = Role::get();
        return view('admin.user.admin', compact('data', 'roles'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit admin');
        $this->validate($request, $this->userRules($this->role, $request, $id));

        $this->userUpdate($this->role, $request, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully updated!');
        return redirect()->route('admin.user.admin.index', qArray());
    }

    public function destroy(Request $request, $id)
    {

        $this->authorize('delete admin');
        $this->userDelete($this->role, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully deleted!');
        return redirect()->route('admin.user.admin.index', qArray());
    }

    public function checkMobile(Request $request)
    {
        $res = $this->checkMobileExists($request->mobile);
        if ($res) {
            return response()->json(['success' => false]);
        }
        return response()->json(['success' => true]);
    }
    public function getUser(Request $request)
    {
        $res =$this->user($request->role)->where('status','Active')->get(['id','name']);
        if ($res) {
            return response()->json(['success' => true, 'data' => $res]);
        }
        return response()->json(['success' => false]);
    }
}