<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = session()->get('cart');

        return inertia('cart/Index', compact('carts'));
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, [
            'product_id' => 'required|integer',
            'color_id' => 'nullable|integer',
            'size_id' => 'nullable|integer',
            'quantity' => 'required|integer',
        ]);
        $sql = ProductItem::with('product', 'color', 'size', 'product.otherInfo')
                ->where('product_id', $validatedData['product_id']);
          if ($validatedData['color_id']) {
                $sql->Where('color_id', $validatedData['color_id']);
            } 
            if ($validatedData['size_id']) {
                $sql->where('size_id', $validatedData['size_id']);
            }
            $product = $sql->first();

        $cart = session()->pull('cart');

        if (isset($cart['items'][$validatedData['product_id']])) {
            $item = $cart['items'][$validatedData['product_id']];
            $item['quantity'] += $validatedData['quantity'];
            session()->flash('successMessage', 'Product quantity updated successfully');
        } else {
            $item['product'] = $product;
            $item['quantity'] = $validatedData['quantity'];
            session()->flash('successMessage', 'Product added to cart successfully');
        }
        $cart['items'][$validatedData['product_id']] = $item;

        $cart['subtotal'] = 0;
        foreach ($cart['items'] as $item) {
            $cart['subtotal'] += $item['quantity'] * $item['product']->sale_price;
        }

        session()->put('cart', $cart);

        if ($request->input('action') == 'buy') {
            return redirect()->route('ecommerce.checkout.index');
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $cart = session()->pull('cart');
        if (!$cart) {
            session()->flash('errorMessage', 'Your cart is empty!');
        }

        if (isset($cart['items'][$id])) {
            unset($cart['items'][$id]);
            $cart['subtotal'] = 0;
            foreach ($cart['items'] as $item) {
                $cart['subtotal'] += $item['quantity'] * $item['product']->unit_price;
            }
            session()->flash('successMessage', 'Product removed from cart successfully');
        }

        session()->put('cart', $cart);

        return redirect()->back();
    }
}
