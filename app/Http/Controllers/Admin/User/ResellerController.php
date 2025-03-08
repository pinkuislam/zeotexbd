<?php

namespace App\Http\Controllers\Admin\User;

use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ResellerService;

class ResellerController extends Controller
{
    use UserTrait;
    protected $role = 'Reseller';

    public function index(Request $request)
    {
        $this->authorize('list reseller');
        $records = $this->users($this->role, $request)->paginate($request->limit ?? config('settings.per_page_limit'));
        return view('admin.user.reseller', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add reseller');
        $roles = Role::get();

        return view('admin.user.reseller', compact('roles'))->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add reseller');
        $this->validate($request, $this->userRules($this->role, $request, null));

        $this->userCreate($request, $request->access_role);

        $request->session()->flash('successMessage', $this->role . ' was successfully added!');
        return redirect()->route('admin.user.reseller.create', qArray());
    }

    public function show($id)
    {
        $this->authorize('show reseller');
        $data = $this->user($this->role)->findOrFail($id);
        return view('admin.user.reseller', compact('data'))->with('show', $id);
    }

    public function edit($id)
    {
        $this->authorize('edit reseller');
        $data = $this->user($this->role)->findOrFail($id);
        $roles = Role::get();
        return view('admin.user.reseller', compact('data', 'roles'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit reseller');
        $this->validate($request, $this->userRules($this->role, $request, $id));

        $this->userUpdate($this->role, $request, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully updated!');
        return redirect()->route('admin.user.reseller.index', qArray());
    }

    public function destroy(Request $request, $id)
    {

        $this->authorize('delete reseller');
        $this->userDelete($this->role, $id);

        $request->session()->flash('successMessage', $this->role . ' was successfully deleted!');
        return redirect()->route('admin.user.reseller.index', qArray());
    }
    public function due(Request $request)
    {
        $due = ResellerService::due($request->id);
        return response()->json(['success' => true, 'due' => $due]);
    }

    public function pricing($id)
    {
        $this->authorize('price_setup reseller');
        $data = $this->user($this->role)->with('userProducts')->findOrFail($id);
        $products = Product::with('unit')->whereIn('product_type', ['Base', 'Combo', 'Product'])->where('status','Active')->get()->map(function($product) use ($data) {
            $product->price = data_get($data->userProducts->firstWhere('id', $product->id), 'pivot.price') ?? null;
            return $product;
        });
        return view('admin.user.reseller-price', compact('data', 'products'))->with('show', $id);
    }

    public function priceUpdate(Request $request, $id)
    {
        $this->authorize('price_setup reseller');
        $this->validate($request, [
            'products' => 'required|array|min:1',
            'products.*' => 'required|numeric',
        ]);
        $data = $this->user($this->role)->findOrFail($id);
        $data->userProducts()->sync($this->mapProduct($request->products));
        $request->session()->flash('successMessage', $this->role . ' price was successfully updated!');
        return redirect()->route('admin.user.reseller.price', $id);
    }
    public function pricingSetup()
    {
        $this->authorize('price_setup reseller');
        $products = Product::with(['category', 'unit'])
        ->whereIn('product_type', ['Base', 'Combo', 'Product'])
        ->where('status','Active')
        ->get();
        $price = 1;
        return view('admin.user.reseller-price-setup', compact('products'))->with('price');
    }

    public function priceSetupUpdate(Request $request)
    {
        $this->authorize('price_setup reseller');
        $this->validate($request, [
            'products' => 'required|array|min:1',
            'products.*' => 'required|numeric',
        ]);
        $resellers = $this->user($this->role)->get(['id']);
        foreach ($resellers as  $data) {
            $data->userProducts()->sync($this->mapProducts($request->products));
        }
        $request->session()->flash('successMessage', $this->role . ' price was successfully updated!');
        return redirect()->route('admin.user.reseller.price.setup');
    }

    private function mapProducts($products)
    {
        return collect($products)->map(function ($i, $v) {
            $data = Product::findOrFail($v);
            $data->update(['reseller_price'=> $i]);
            return ['price' => $i];
        });
    }
    private function mapProduct($products)
    {
        return collect($products)->map(function ($i, $v) {
            return ['price' => $i];
        });
    }

}