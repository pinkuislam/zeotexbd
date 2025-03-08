<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

//Test Route::START
Route::group(['prefix' => 'test'], function () {
    Route::get('command', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        dd("All clear!");
    });

    Route::get('/storage-link', function () {
        Artisan::call('storage:link');
    });
});
//Test Route::END

Route::get('/error/{type}', function ($type) {
    return view('errors.' . $type);
})->name('error');

Auth::routes(['verify' => true, 'register' => false]);
Route::get('/', 'App\Http\Controllers\Auth\LoginController@showLoginForm');

Route::group(['namespace' => 'App\Http\Controllers\Admin', 'as' => 'admin.'], function () {
    Route::group(['middleware' => ['auth', 'verified', 'valid.auth']], function () {
        Route::get('no-access', 'DashboardController@noAccess')->name('no-access');
        Route::get('profile', 'ProfileController@index')->name('profile');
        Route::post('profile', 'ProfileController@update');
        Route::get('profile/password', 'ProfileController@password')->name('profile.password');
        Route::post('profile/password', 'ProfileController@passwordUpdate');

        Route::resource('role', 'RoleController')->except('show');
        Route::resource('activity-log', 'ActivityLogController');

        Route::group(['namespace' => 'Sale'], function () {
            Route::get('orders/{order}/snap', 'OrderController@snap')->name('orders.snap');
            Route::resource('orders', 'OrderController');
            Route::get('order-print', 'OrderController@orderPrint')->name('orders.print');
            Route::get('pending-sale', 'OrderController@pendingSale')->name('pending.sale');
            Route::get('get-order', 'OrderController@getOrder')->name('getorder');
            Route::get('payment-order', 'OrderController@paymentOrder')->name('payment.order');
        });
        Route::group(['namespace' => 'Sale', 'prefix' => 'sale', 'as' => 'sale.'], function () {
            Route::resource('sales', 'SaleController');
            Route::get('get-order', 'SaleController@getOrder')->name('getorder');
            Route::post('delivery-change', 'SaleController@updataSale')->name('delivery.ajax-store');
            Route::get('sales/invoice/{id}', 'SaleController@invoice')->name('sales.invoice');
            Route::get('sales/canceled/{id}', 'SaleController@saleCanceled')->name('sales.cancel');
            Route::get('pending-delivery', 'SaleController@pendingDelivery')->name('pending.delivery');
            Route::get('delivered-sale', 'SaleController@deliveredSale')->name('sale.delivered');
            Route::get('get-sale', 'SaleReturnController@getSale')->name('getsale');
            Route::resource('return', 'SaleReturnController');
        });
        Route::group(['namespace' => 'Purchase', 'prefix' => 'purchase', 'as' => 'purchase.'], function () {
            Route::resource('raw', 'RawMetarialController');
            Route::get('get-supplier-purchase', 'RawMetarialController@getSupplierPurchase')->name('supplier.purchase');
            Route::get('get-purchase', 'RawMetarialController@getPurchase')->name('getpurchase');
            Route::get('finished-order-base', 'FinishedController@getOrderbasePurchase')->name('finished.order');
            Route::resource('finished', 'FinishedController');
            Route::resource('order-base-turkey', 'OrderBaseTurkeyPurchaseController');
        });
        Route::group(['namespace' => 'PurchaseReturn', 'prefix' => 'purchase-return', 'as' => 'purchase-return.'], function () {
            Route::resource('raw', 'RawMetarialReturnController');
            Route::resource('finished', 'FinishedReturnController');
            Route::get('finished-stock', 'FinishedReturnController@getProductStock')->name('finished.stock');
        });

        Route::group(['namespace' => 'Damage', 'prefix' => 'damage', 'as' => 'damage.'], function () {
            Route::resource('raw', 'RawMetarialDamageController');
            Route::resource('finished', 'FinishedDamageController');
        });

        Route::group(['namespace' => 'Dyeing'], function () {
            Route::get('get-agent-stock', 'SendDyeingController@getAgentStock')->name('dyeing.stock');
            Route::resource('send-dyeing', 'SendDyeingController');
            Route::resource('receive-dyeing', 'ReceiveDyeingController');
        });

        Route::group(['namespace' => 'Production', 'prefix' => 'production', 'as' => 'production.'], function () {
            Route::get('get-raw-stock', 'ProductionController@getRawStock')->name('getrawstock');
            Route::get('get-order-production', 'OrderBaseProductionController@getOrder')->name('order');
            Route::resource('/', 'ProductionController');
            Route::resource('order-base', 'OrderBaseProductionController');
        });

        Route::group(['namespace' => 'Payment', 'prefix' => 'payment', 'as' => 'payment.'], function () {

            Route::resource('income', 'IncomeController');
            Route::resource('expense', 'ExpenseController');

            Route::get('customer-payments/adjustment', 'CustomerPaymentController@adjustment')->name('customer-payments.adjustment');
            Route::get('customer-payments/receive', 'CustomerPaymentController@receive')->name('customer-payments.receive');
            Route::get('customer-payments/bulk-receive', 'CustomerPaymentController@bulkReceive')->name('customer-payments.bulk-receive');
            Route::post('customer-payments/bulk-receive', 'CustomerPaymentController@bulkReceiveStore')->name('customer-payments.bulk-receive.store');
            Route::put('customer-payment/{id}/approve', 'CustomerPaymentController@approve')->name('customer-payments.approve');
            Route::resource('customer-payments', 'CustomerPaymentController');

            Route::get('supplier-payments/adjustment', 'SupplierPaymentController@adjustment')->name('supplier-payments.adjustment');
            Route::get('supplier-payments/receive', 'SupplierPaymentController@receive')->name('supplier-payments.receive');
            Route::put('supplier-payment/{id}/approve', 'SupplierPaymentController@approve')->name('supplier-payments.approve');
            Route::resource('supplier-payments', 'SupplierPaymentController');

            Route::get('reseller-payments/adjustment', 'ResellerPaymentController@adjustment')->name('reseller-payments.adjustment');
            Route::get('reseller-payments/receive', 'ResellerPaymentController@receive')->name('reseller-payments.receive');
            Route::put('reseller-payment/{id}/approve', 'ResellerPaymentController@approve')->name('reseller-payments.approve');
            Route::resource('reseller-payments', 'ResellerPaymentController');

            Route::get('reseller-business-payments/adjustment', 'ResellerBusinessPaymentController@adjustment')->name('reseller-business-payments.adjustment');
            Route::get('reseller-business-payments/receive', 'ResellerBusinessPaymentController@receive')->name('reseller-business-payments.receive');
            Route::put('reseller-business-payment/{id}/approve', 'ResellerBusinessPaymentController@approve')->name('reseller-business-payments.approve');
            Route::resource('reseller-business-payments', 'ResellerBusinessPaymentController');

            Route::get('delivery-agent-payments/adjustment', 'DeliveryAgentPaymentController@adjustment')->name('delivery-agent-payments.adjustment');
            Route::get('delivery-agent-payments/receive', 'DeliveryAgentPaymentController@receive')->name('delivery-agent-payments.receive');
            Route::put('delivery-agent-payment/{id}/approve', 'DeliveryAgentPaymentController@approve')->name('delivery-agent-payments.approve');
            Route::resource('delivery-agent-payments', 'DeliveryAgentPaymentController');

            Route::get('dyeing-payments/adjustment', 'DyeingAgentPaymentController@adjustment')->name('dyeing-payments.adjustment');
            Route::get('dyeing-payments/receive', 'DyeingAgentPaymentController@receive')->name('dyeing-payments.receive');
            Route::put('dyeing-payment/{id}/approve', 'DyeingAgentPaymentController@approve')->name('dyeing-payments.approve');
            Route::resource('dyeing-payments', 'DyeingAgentPaymentController');

            Route::get('seller-payments/adjustment', 'SellerCommissionController@adjustment')->name('seller-payments.adjustment');
            Route::get('seller-payments/receive', 'SellerCommissionController@receive')->name('seller-payments.receive');
            Route::put('seller-payment/{id}/approve', 'SellerCommissionController@approve')->name('seller-payments.approve');
            Route::resource('seller-payments', 'SellerCommissionController');

            Route::put('fund-transfers/{id}/approve', 'FundTransferController@approve')->name('fund-transfers.approve');
            Route::resource('fund-transfers', 'FundTransferController');
            Route::resource('invest', 'InvestController');
            Route::get('loan-payments/adjustment', 'LoanPaymentController@adjustment')->name('loan-payments.adjustment');
            Route::resource('loan-payments', 'LoanPaymentController');
        });

        Route::group(['namespace' => 'Basic', 'prefix' => 'basic', 'as' => 'basic.'], function () {
            Route::post('unit-import', 'UnitController@import')->name('unit.import');
            Route::get('unit-export', 'UnitController@export')->name('unit.export');
            Route::put('unit/{id}/status', 'UnitController@statusChange')->name('unit.status');
            Route::resource('unit', 'UnitController');

            Route::post('size-import', 'SizeController@import')->name('size.import');
            Route::get('size-export', 'SizeController@export')->name('size.export');
            Route::put('size/{id}/status', 'SizeController@statusChange')->name('size.status');
            Route::resource('size', 'SizeController');

            Route::post('color-import', 'ColorController@import')->name('color.import');
            Route::get('color-export', 'ColorController@export')->name('color.export');
            Route::put('color/{id}/status', 'ColorController@statusChange')->name('color.status');
            Route::resource('color', 'ColorController');

            Route::post('category-import', 'CategoryController@import')->name('category.import');
            Route::get('category-export', 'CategoryController@export')->name('category.export');
            Route::put('category/{id}/status', 'CategoryController@statusChange')->name('category.status');
            Route::resource('category', 'CategoryController');

            Route::delete('products/cover/ecommerce/{id}', 'ProductCoverController@destroyEcommerceData')->name('cover-products.ecommerce.destroy');
            Route::post('products/cover/image-destroy', 'ProductCoverController@destroyImage')->name('cover-products.image-destroy');
            Route::post('products/cover/ecommerce/store', 'ProductCoverController@ecommerceStore')->name('cover-products.ecommerce.store');
            Route::get('product-ecommerce/cover/{id}', 'ProductCoverController@ecommerce')->name('cover-product.ecommerce');
            Route::put('product/cover/{id}/status', 'ProductCoverController@statusChange')->name('cover-product.status');
            Route::get('get-raw-product', 'ProductCoverController@getRawProduct')->name('raw.product');
            Route::get('get-base-product', 'ProductCoverController@getBaseProduct')->name('base.product');
            Route::resource('product-cover', 'ProductCoverController');
            
            Route::delete('products/other/ecommerce/{id}', 'ProductOtherController@destroyEcommerceData')->name('other-products.ecommerce.destroy');
            Route::post('products/other/image-destroy', 'ProductOtherController@destroyImage')->name('other-products.image-destroy');
            Route::post('products/other/ecommerce/store', 'ProductOtherController@ecommerceStore')->name('other-products.ecommerce.store');
            Route::get('product-ecommerce/other/{id}', 'ProductOtherController@ecommerce')->name('other-product.ecommerce');
            Route::put('product/other/{id}/status', 'ProductOtherController@statusChange')->name('other-product.status');
            Route::resource('product-other', 'ProductOtherController');

            Route::get('shipping-charge', 'ShippingMethodController@shippingCharge')->name('shippingcharge');
            Route::post('shipping-method-import', 'ShippingMethodController@import')->name('shipping_method.import');
            Route::get('shipping-method-export', 'ShippingMethodController@export')->name('shipping_method.export');
            Route::put('shipping-method/{id}/status', 'ShippingMethodController@statusChange')->name('shipping_method.status');
            Route::resource('shipping_method', 'ShippingMethodController');

            Route::put('bank/{id}/status', 'BankController@statusChange')->name('bank.status');
            Route::get('bank/due', 'BankController@due')->name('bank.due');
            Route::resource('bank', 'BankController');

            Route::resource('income-category', 'IncomeCategoryController');
            Route::resource('expense-category', 'ExpenseCategoryController');
        });

        Route::group(['namespace' => 'Asset', 'prefix' => 'asset', 'as' => 'asset.'], function () {
            Route::post('assets-import', 'AssetController@import')->name('assets.import');
            Route::get('assets-export', 'AssetController@export')->name('assets.export');
            Route::put('assets/{id}/status', 'AssetController@statusChange')->name('assets.status');
            Route::resource('assets', 'AssetController');
            Route::resource('asset-items', 'AssetItemController');
            Route::get('ledger', 'AssetController@ledger')->name('ledger');;
        });
        Route::group(['namespace' => 'Accessory', 'prefix' => 'accessory', 'as' => 'accessory.'], function () {
            Route::post('accessories-import', 'AccessoryController@import')->name('accessories.import');
            Route::get('accessories-export', 'AccessoryController@export')->name('accessories.export');
            Route::put('accessories/{id}/status', 'AccessoryController@statusChange')->name('accessories.status');
            Route::resource('accessories', 'AccessoryController');
            Route::resource('purchase', 'AccessoryStockController');
            Route::resource('purchase_returns', 'AccessoryStockReturnController');
            Route::resource('consume', 'AccessoryConsumeController');
            Route::get('get-supplier-purchase', 'AccessoryStockController@getSupplierPurchase')->name('supplier.purchase');
            Route::get('get-purchase', 'AccessoryStockController@getPurchase')->name('getpurchase');
            Route::get('ledger', 'AccessoryController@ledger')->name('ledger');
            Route::get('ledger-details', 'AccessoryController@ledgerDetails')->name('ledger.details');
        });

        Route::group(['namespace' => 'Ecommerce', 'prefix' => 'ecommerce', 'as' => 'ecommerce.'], function () {
            Route::resource('sliders', 'SliderController');
            Route::resource('settings', 'SiteSettingController');
            Route::resource('pages', 'PageController');
            Route::resource('highlights', 'HighlightController');
            Route::resource('faq', 'FaqController');
            Route::put('orders/{id}/status/{sts}', 'OrderController@status')->name('orders.status');
            Route::resource('orders', 'OrderController');
        });

        Route::get('dashboard', 'Report\CommonController@dashboard')->name('dashboard');

        Route::group(['namespace' => 'User', 'prefix' => 'user', 'as' => 'user.'], function () {
            Route::resource('admin', 'AdminController');
            Route::resource('seller', 'SellerController');
            Route::get('sellers/due', 'SellerController@due')->name('sellers.due');
            Route::get('reseller/{id}/price', 'ResellerController@pricing')->name('reseller.price');
            Route::post('reseller/{id}/price', 'ResellerController@priceUpdate');
            Route::get('reseller/price-setup', 'ResellerController@pricingSetup')->name('reseller.price.setup');
            Route::post('reseller/price-setup', 'ResellerController@priceSetupUpdate')->name('reseller.price.setup.update');
            Route::resource('reseller', 'ResellerController');
            Route::get('resellers/due', 'ResellerController@due')->name('resellers.due');
            Route::get('get-reseller-business', 'ResellerBusinessController@getResellerBusiness')->name('getreseller.business');
            Route::get('reseller_business/due', 'ResellerBusinessController@due')->name('reseller_business.due');
            Route::resource('reseller_business', 'ResellerBusinessController');
            Route::resource('staff', 'StaffController');
            Route::put('supplier/{id}/status', 'SupplierController@statusChange')->name('supplier.status');
            Route::get('suppliers/due', 'SupplierController@due')->name('suppliers.due');
            Route::resource('supplier', 'SupplierController');
            Route::get('customers/history/{id}', 'CustomerController@customerHistory')->name('customer.history');
            Route::get('customers/due', 'CustomerController@due')->name('customers.due');
            Route::put('customer/{id}/status', 'CustomerController@statusChange')->name('customer.status');
            Route::post('customer-import', 'CustomerController@import')->name('customer.import');
            Route::get('customer-export', 'CustomerController@export')->name('customer.export');
            Route::post('customer-ajax-store', 'CustomerController@ajaxStore')->name('customers.ajax-store');
            Route::get('get-customer', 'CustomerController@getCustomer')->name('getcustomer');
            Route::get('single-customer', 'CustomerController@getSingleCustomer')->name('getsinglecustomer');
            Route::resource('customer', 'CustomerController');
            Route::put('delivery-agent/{id}/status', 'DeliveryAgentController@statusChange')->name('delivery_agent.status');
            Route::get('delivery-agents/due', 'DeliveryAgentController@due')->name('delivery-agents.due');
            Route::get('get-user', 'AdminController@getUser')->name('getuser');
            Route::resource('delivery_agent', 'DeliveryAgentController');
            Route::resource('investor', 'InvestorController');
            Route::get('loanholders/due', 'LoanHolderController@due')->name('loanholders.due');
            Route::resource('loan-holder', 'LoanHolderController');
            
            Route::get('dyeing-agent/due', 'DyeingAgentController@due')->name('dyeing-agent.due');
            Route::put('dyeing-agent/{id}/status', 'DyeingAgentController@statusChange')->name('dyeing-agent.status');
            Route::resource('dyeing-agent', 'DyeingAgentController');
        });

        Route::group(['namespace' => 'Report', 'prefix' => 'report', 'as' => 'report.'], function () {
            Route::get('product-stock/raw-material', 'CommonController@rawStock')->name('product-stock');
            Route::get('finished-product-stock/{type}', 'CommonController@finishedStock')->name('finished-product-stock');
            Route::get('finished-product-ledger', 'CommonController@finishedLedger')->name('finished-product-ledger');
            Route::get('product-ledger', 'CommonController@ledger')->name('product-ledger');

            Route::get('orders', 'CommonController@order')->name('orders');

            Route::get('bank', 'CommonController@bank')->name('bank');
            Route::get('bank-transactions', 'CommonController@bankTransactions')->name('bank-transactions');

            Route::get('supplier', 'CommonController@supplier')->name('supplier');
            Route::get('supplier-transactions', 'CommonController@supplierTransactions')->name('supplier-transactions');

            Route::get('customer', 'CommonController@customer')->name('customer');
            Route::get('customer-transactions', 'CommonController@customerTransactions')->name('customer-transactions');

            Route::get('seller', 'CommonController@seller')->name('seller');
            Route::get('seller-transactions', 'CommonController@sellerTransactions')->name('seller-transactions');

            Route::get('reseller', 'CommonController@reseller')->name('reseller');
            Route::get('reseller-transactions', 'CommonController@resellerTransactions')->name('reseller-transactions');

            Route::get('reseller-business', 'CommonController@resellerBusiness')->name('reseller-business');
            Route::get('reseller-business-transactions', 'CommonController@resellerBusinessTransactions')->name('reseller-business-transactions');

            Route::get('delivery-agent', 'CommonController@deliveryAgent')->name('delivery-agent');
            Route::get('delivery-agent-transactions', 'CommonController@deliveryAgentTransactions')->name('delivery-agent-transactions');

            Route::get('dyeing-agent', 'CommonController@dyeingAgent')->name('dyeing-agent');
            Route::get('dyeing-agent-transactions', 'CommonController@dyeingAgentTransactions')->name('dyeing-agent-transactions');

            Route::get('expense', 'CommonController@expense')->name('expense');
            Route::get('income', 'CommonController@income')->name('income');

            Route::get('income-statement', 'CommonController@incomeStatement')->name('income-statement');
            Route::get('balance-sheet', 'CommonController@trialBalance')->name('balance-sheet');
        });
    });
});
