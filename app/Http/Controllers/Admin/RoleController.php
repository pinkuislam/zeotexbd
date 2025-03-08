<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list role');

        $sql = Role::where('name', '!=', 'Super Admin')->orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where('name', 'LIKE', $request->q.'%');
        }

        $records = $sql->paginate($request->limit ?? 15);

        return view('admin.role', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add role');

        $permissions = Permission::get();
        $permissionArr = [];
        foreach($permissions as $per) {
            $permissionArr[$per->module_name][] = (object) [
                'id' => $per->id,
                'name' => $per->name,
            ];
        }

        $rolePermissions = [];
        
        return view('admin.role', compact('permissionArr', 'rolePermissions'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add role');

        $this->validate($request, [
            'name' => 'required|max:100',
            'permissions' => 'required|array|min:1',
        ]);

        $storeData = [
            'name' => $request->name,
        ];
        $data = Role::create($storeData);
        $data->syncPermissions($request->permissions);

        $request->session()->flash('successMessage', 'Role was successfully added');
        return redirect()->route('admin.role.create', qArray());
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit role');

        $data = Role::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Row not found!');
            return redirect()->route('admin.role.index', qArray());
        }

        $permissions = Permission::get();
        $permissionArr = [];
        foreach($permissions as $per) {
            $permissionArr[$per->module_name][] = (object) [
                'id' => $per->id,
                'name' => $per->name,
            ];
        }

        $rolePermissions = $data->getPermissionNames()->toArray();

        return view('admin.role', compact('data', 'permissionArr', 'rolePermissions'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit role');

        $this->validate($request, [
            'name' => 'required|max:100',
            'permissions' => 'required|array|min:1',
        ]);

        $data = Role::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Row not found!');
            return redirect()->route('admin.role.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
        ];

        $data->update($storeData);
        $data->syncPermissions($request->permissions);

        $request->session()->flash('successMessage', 'Role was successfully updated');
        return redirect()->route('admin.role.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete role');

        $data = Role::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Row not found!');
            return redirect()->route('admin.role.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Role was successfully deleted');
        return redirect()->route('admin.role.index', qArray());
    }
}
