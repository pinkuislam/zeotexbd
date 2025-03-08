<?php

namespace App\Http\Controllers\Admin\User;

use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Services\SellerService;

class SellerController extends Controller
{
    use UserTrait;
    protected $role = 'Seller';

    public function index(Request $request)
    {
        $this->authorize('list seller');
        $records = $this->users($this->role, $request)->paginate($request->limit ?? config('settings.per_page_limit'));
        return view('admin.user.seller', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add seller');
        $roles = Role::get();

        return view('admin.user.seller', compact('roles'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add seller');
        $this->validate($request, $this->userRules($this->role, $request, null));

        $this->userCreate($request, $request->access_role);

        $request->session()->flash('successMessage', $this->role . ' was successfully added!');
        return redirect()->route('admin.user.seller.create', qArray());
    }

    public function show($id)
    {
        $this->authorize('show seller');
        $data = $this->user($this->role)->findOrFail($id);
        return view('admin.user.seller', compact('data'))->with('show', $id);
    }

    public function edit($id)
    {
        $this->authorize('edit seller');
        $data = $this->user($this->role)->findOrFail($id);
        $roles = Role::get();
        return view('admin.user.seller', compact('data', 'roles'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit seller');
        $this->validate($request, $this->userRules($this->role, $request, $id));

        $this->userUpdate($this->role, $request, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully updated!');
        return redirect()->route('admin.user.seller.index', qArray());
    }

    public function destroy(Request $request, $id)
    {

        $this->authorize('delete seller');
        $this->userDelete($this->role, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully deleted!');
        return redirect()->route('admin.user.seller.index', qArray());
    }
    public function due(Request $request)
    {
        $due = SellerService::due($request->id);
        return response()->json(['success' => true, 'due' => $due]);
    }
}