<?php

namespace App\Http\Controllers\Api\Basic;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankCollection;
use App\Http\Resources\BankResource;
use App\Http\Resources\ColorCollection;
use App\Http\Resources\ColorResource;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShippingMethodCollection;
use App\Http\Resources\ShippingMethodResource;
use App\Http\Resources\UnitCollection;
use App\Http\Resources\UnitResource;
use App\Models\Bank;
use App\Models\Color;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Models\Unit;
use App\Models\ProductItem;
use Exception;
use Illuminate\Http\Request;

class BasicDataController extends Controller
{
/**
     * @authenticated
     * @responseFile responses/basic/unit.json
     */
    public function getUnit()
    {
        try {
            $units = new UnitCollection(Unit::all());
            return response()->json([
                'status' => true,
                'message' => 'Units Request Successfully.',
                'data' => $units,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
     /**
     * @authenticated
     * @responseFile responses/basic/single-unit.json
     */
    public function singleUnit($id)
    {
        try {
            $unit = new UnitResource(Unit::findOrFail($id));
            return response()->json([
                'status' => true,
                'message' => 'Unit Request Successfully.',
                'data' => $unit,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
       /**
     * @authenticated
     * @responseFile responses/basic/color.json
     */
    public function getColor()
    {
        try {
            $colors = new ColorCollection(Color::all());
            return response()->json([
                'status' => true,
                'message' => 'Colors Request Successfully.',
                'data' => $colors,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
       /**
     * @authenticated
     * @responseFile responses/basic/single-color.json
     */
    public function singleColor($id)
    {
        try {
            $color = new ColorResource(Color::findOrFail($id));
            return response()->json([
                'status' => true,
                'message' => 'Color Request Successfully.',
                'data' => $color,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
       /**
     * @authenticated
     * @responseFile responses/basic/product.json
     */
    public function getProduct()
    {
        try {
            $products = new ProductCollection(Product::with('unit')->get());
            return response()->json([
                'status' => true,
                'message' => 'Products Request Successfully.',
                'data' => $products,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
       /**
     * @authenticated
     * @responseFile responses/basic/single-product.json
     */
    public function singleProduct($id)
    {
        try {
            $product = new ProductResource(Product::findOrFail($id));
            return response()->json([
                'status' => true,
                'message' => 'Product Request Successfully.',
                'data' => $product,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
       /**
     * @authenticated
     * @responseFile responses/basic/bank.json
     */
    public function getBank()
    {
        try {
            $banks = new BankCollection(Bank::all());
            return response()->json([
                'status' => true,
                'message' => 'banks Request Successfully.',
                'data' => $banks,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
       /**
     * @authenticated
     * @responseFile responses/basic/single-bank.json
     */
    public function singleBank($id)
    {
        try {
            $bank = new BankResource(Bank::findOrFail($id));
            return response()->json([
                'status' => true,
                'message' => 'Bank Request Successfully.',
                'data' => $bank,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
       /**
     * @authenticated
     * @responseFile responses/basic/shipping-methods.json
     */
    public function getShippingMethod()
    {
        try {
            $shipping_methods = new ShippingMethodCollection(ShippingRate::all());
            return response()->json([
                'status' => true,
                'message' => 'Shipping Methods Request Successfully.',
                'data' => $shipping_methods,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
    /**
     * @authenticated
     * @responseFile responses/basic/single-shhipping-method.json
     */
    public function singleShippingMethod($id)
    {
        try {
            $shipping_methods = new ShippingMethodResource(ShippingRate::findOrFail($id));
            return response()->json([
                'status' => true,
                'message' => 'Shipping Method Request Successfully.',
                'data' => $shipping_methods,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
}

