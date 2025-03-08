<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'sudip.me') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('admin-assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/datetimepicker/jquery.datetimepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/fancybox-3.0/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/datatables/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/datatables/export/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/styles.css') }}">
    @stack('styles')
</head>

<body class="hold-transition skin-black sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <span class="logo-mini">
                    {{ config('app.name', 'Laravel') }}
                </span>
                <span class="logo-lg">
                    {{ config('app.name', 'Laravel') }}
                </span>
            </a>

            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <span class="project-name-header"></span>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{ asset('admin-assets/images/avatar.png') }}" alt="avatar"
                                    class="user-image">
                                <span class="hidden-xs">
                                    {{ Auth::user()->name }}
                                </span>
                            </a>

                            <ul class="dropdown-menu">

                                <li class="user-header">
                                    <img src="{{ asset('admin-assets/images/avatar.png') }}" alt="avatar"
                                        class="img-circle">
                                    <p>
                                        {{ Auth::user()->name }}
                                        <small>
                                            {{ Auth::user()->mobile }}<br>
                                            {{ Auth::user()->email }}
                                        </small>
                                    </p>
                                </li>

                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a class="btn btn-custom btn-flat" href="{{ route('admin.profile') }}">My
                                            Account</a>
                                    </div>
                                    <div class="pull-right">
                                        <a class="btn btn-custom btn-flat" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" class="non-validate" action="{{ route('logout') }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        
        <aside class="main-sidebar" style="position:fixed; overflow-y:scroll; height:100vh">
            <section class="sidebar">
                <ul class="sidebar-menu">
                    <li class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @canany(['list purchase', 'add purchase'])
                        <li class="treeview {{ Request::routeIs('admin.purchase.*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-shopping-cart"></i>
                                <span>Purchase</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                @canany(['list purchase', 'add purchase'])
                                    <li class="{{ Request::routeIs('admin.purchase.raw.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.purchase.raw.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Fabrics </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list purchase', 'add purchase-finished'])
                                    <li class="{{ Request::routeIs('admin.purchase.order-base-turkey.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.purchase.order-base-turkey.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Order Base Turkey </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list purchase', 'add purchase-finished'])
                                    <li class="{{ Request::routeIs('admin.purchase.finished.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.purchase.finished.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Other Products </span>
                                        </a>
                                    </li>
                                @endcanany
                        
                            </ul>
                        </li>
                    @endcanany
                    
                    @canany(['list send-dyeing', 'add send-dyeing' , 'list receive-dyeing', 'add receive-dyeing'])
                    <li class="treeview {{ Request::routeIs('admin.send-dyeing.*') || Request::routeIs('admin.receive-dyeing.*') ? 'active menu-open' : '' }}">
                        <a href="#">
                            <i class="fa fa-book"></i>
                            <span>Dyeing</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @canany(['list send-dyeing', 'add send-dyeing'])
                                <li class="{{ Request::routeIs('admin.send-dyeing.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.send-dyeing.index') }}"><i class="fa fa-circle-o"></i> Send</a>
                                </li>
                            @endcanany
                            @canany(['list receive-dyeing', 'add receive-dyeing'])
                                <li class="{{ Request::routeIs('admin.receive-dyeing.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.receive-dyeing.index') }}"><i class="fa fa-circle-o"></i> Receive</a>
                                </li>
                            @endcanany
                        </ul>
                    </li>   
                @endcanany

                    @canany(['list purchase-return', 'add purchase-return'])
                        <li class="treeview {{ Request::routeIs('admin.purchase-return*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-shopping-cart"></i>
                                <span>Purchase Return</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                @canany(['list purchase-return', 'add purchase-return'])
                                    <li class="{{ Request::routeIs('admin.purchase-return.raw.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.purchase-return.raw.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Fabrics </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list purchase-return', 'add purchase-return'])
                                    <li class="{{ Request::routeIs('admin.purchase-return.finished.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.purchase-return.finished.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Other Products </span>
                                        </a>
                                    </li>
                                @endcanany
                        
                            </ul>
                        </li>
                    @endcanany
                    @canany(['list production', 'add production'])
                    <li class="treeview {{ Request::routeIs('admin.production*') ? 'active' : '' }}">
                        <a href="{{ route('admin.production.order-base.index') }}">
                            <i class="fa fa-product-hunt"></i>
                            <span> Production </span>
                        </a>
                    </li>
                    @endcanany
                    @canany(['list orders', 'add orders' , 'print orders'])
                    <li class="treeview {{ Request::routeIs('admin.orders.*') ? 'active menu-open' : '' }}">
                        <a href="#">
                            <i class="fa fa-book"></i>
                            <span>Order</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @canany(['list orders', 'add orders'])
                                <li class="{{ Request::routeIs('admin.orders.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.orders.index') }}"><i class="fa fa-circle-o"></i> Orders</a>
                                </li>
                            @endcanany
                            @canany(['print orders'])
                                <li class="{{ Request::routeIs('admin.orders.print') ? 'active' : '' }}">
                                    <a href="{{ route('admin.orders.print') }}"><i class="fa fa-circle-o"></i> Order Print</a>
                                </li>
                            @endcanany
                        </ul>
                    </li>   
                @endcanany
                @canany(['list sale', 'add sale', 'list sale-return', 'add sale-return'])
                <li class="treeview {{ Request::routeIs('admin.sale.*') ? 'active menu-open' : '' }}">
                        <a href="#">
                            <i class="fa fa-tag"></i>
                            <span>Sales</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>

                        <ul class="treeview-menu">
                            @canany(['list sale', 'add sale'])
                            <li class="{{ Request::routeIs('admin.sale.sales.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.sale.sales.index') }}"><i class="fa fa-circle-o"></i> Sale</a>
                                </li>
                                @endcanany
                                @canany(['list sale-return', 'add sale-return'])
                                <li class="{{ Request::routeIs('admin.sale.return.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.sale.return.index') }}"><i class="fa fa-circle-o"></i> Sale Return</a>
                                </li>
                                @endcanany
                        </ul>
                    </li>   
                    @endcanany
                    @canany(['list damage', 'add damage'])
                        <li class="treeview {{ Request::routeIs('admin.damage.*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-trash-o"></i>
                                <span>Damage</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @canany(['list damage', 'add damage'])
                                    <li class="{{ Request::routeIs('admin.damage.raw.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.damage.raw.index') }}"><i class="fa fa-circle-o"></i> Fabric Damage</a>
                                    </li>
                                    <li class="{{ Request::routeIs('admin.damage.finished.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.damage.finished.index') }}"><i class="fa fa-circle-o"></i> Finished Damage</a>
                                    </li>
                                @endcanany
                            </ul>
                        </li>   
                    @endcanany
                    @canany([
                        'list income', 'add income', 'list expense', 'add expense', 'list customer-payment', 'add customer-payment', 'list supplier-payment', 'add supplier-payment', 'list seller-commission', 'add seller-commission', 'list reseller-payment', 'add reseller-payment', 'list reseller-business-payment', 'add reseller-business-payment', 'list delivery-agent-payment', 'add delivery-agent-payment', 'list fund-transfer', 'add fund-transfer', 'list invest', 'add invest',
                        ])
                        <li class="treeview {{ Request::routeIs('admin.payment.*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-money"></i>
                                <span>Payment</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                @canany(['list income', 'add income'])
                                    <li class="{{ Request::routeIs('admin.payment.income.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.income.create') }}"><i class="fa fa-circle-o"></i> Incomes</a>
                                    </li>
                                @endcanany

                                @canany(['list expense', 'add expense'])
                                    <li class="{{ Request::routeIs('admin.payment.expense.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.expense.create') }}"><i class="fa fa-circle-o"></i> Expenses</a>
                                    </li>
                                @endcanany

                                @canany(['list customer-payment', 'add customer-payment'])
                                    <li class="{{ Request::routeIs('admin.payment.customer-payments.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.customer-payments.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Customer Payment </span>
                                        </a>
                                    </li>
                                @endcanany

                                @canany(['list supplier-payment', 'add supplier-payment'])
                                    <li class="{{ Request::routeIs('admin.payment.supplier-payments.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.supplier-payments.create') }}"><i class="fa fa-circle-o"></i> Supplier Payments</a>
                                    </li>
                                @endcanany
                                @canany(['list loan', 'add loan'])
                                    <li class="{{ Request::routeIs('admin.payment.loan-payments.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.loan-payments.create') }}"><i class="fa fa-circle-o"></i> Loan Payments</a>
                                    </li>
                                @endcanany
                                {{-- @canany(['list seller-payment', 'add seller-payment'])
                                <li class="{{ Request::routeIs('admin.payment.seller-payments.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.payment.seller-payments.create') }}"><i class="fa fa-circle-o"></i> Seller Payment</a>
                                </li>
                                @endcanany --}}
                                @canany(['list reseller-payment', 'add reseller-payment'])
                                    <li class="{{ Request::routeIs('admin.payment.reseller-payments.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.reseller-payments.create') }}"><i class="fa fa-circle-o"></i> Reseller Payments</a>
                                    </li>
                                @endcanany
                                @canany(['list reseller-business-payment', 'add reseller-business-payment'])
                                    <li class="{{ Request::routeIs('admin.payment.reseller-business-payments.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.reseller-business-payments.create') }}"><i class="fa fa-circle-o"></i> Reseller Business Payments</a>
                                    </li>
                                @endcanany
                                @canany(['list delivery-agent-payment', 'add delivery-agent-payment'])
                                    <li class="{{ Request::routeIs('admin.payment.delivery-agent-payments.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.delivery-agent-payments.create') }}"><i class="fa fa-circle-o"></i> Delivery Agent Payments</a>
                                    </li>
                                @endcanany

                                
                                @canany(['list dyeing-agent-payment', 'add dyeing-agent-payment'])
                                    <li class="{{ Request::routeIs('admin.payment.dyeing-payments.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.dyeing-payments.create') }}"><i class="fa fa-circle-o"></i> Dyeing Agent Payments</a>
                                    </li>
                                @endcanany

                                @canany(['list fund-transfer', 'add fund-transfer'])
                                    <li class="{{ Request::routeIs('admin.payment.fund-transfers.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.fund-transfers.create') }}"><i class="fa fa-circle-o"></i> Fund Transfer</a>
                                    </li>
                                @endcanany
                                @canany(['list invest', 'add invest'])
                                    <li class="{{ Request::routeIs('admin.payment.invest.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.payment.invest.create') }}"><i class="fa fa-circle-o"></i> Invest</a>
                                    </li>
                                @endcanany
                            </ul>
                        </li>
                    @endcanany
                    @canany([
                        'list unit', 'add unit', 'list size', 'add size', 'list category', 'add category', 'list product', 'add product', 'list color', 'add color', 'list shipping_method', 'add shipping_method', 'list bank', 'add bank', 'list income_category', 'add income_category', 'list expense_category', 'add expense_category'
                        ])
                        <li class="treeview {{ Request::routeIs('admin.basic.*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-cogs"></i>
                                <span>Basic</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                @canany(['list color', 'add color'])
                                    <li class="{{ Request::routeIs('admin.basic.color.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.basic.color.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Color </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list unit', 'add unit'])
                                    <li class="{{ Request::routeIs('admin.basic.unit.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.basic.unit.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Unit </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list size', 'add size'])
                                    <li class="{{ Request::routeIs('admin.basic.size.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.basic.size.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Size </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list category', 'add category'])
                                    <li class="{{ Request::routeIs('admin.basic.category.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.basic.category.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Category </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list product', 'add product'])
                                    <li class="{{ Request::routeIs('admin.basic.product-cover.*') || Request::routeIs('admin.basic.product-other.*') ? 'active' : '' }}">
                                        <a href="#">
                                            <i class="fa fa-circle-o"></i>
                                            <span>Products</span>
                                            <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                        </a>
                                        
                                        <ul class="treeview-menu">
                                                @can('list product')
                                                    <li class="{{ Request::routeIs('admin.basic.product-cover.*') ? 'active' : '' }}">
                                                        <a href="{{ route('admin.basic.product-cover.index') }}">
                                                            <i class="fa fa-circle-o"></i>
                                                            <span> Cover Products </span>
                                                        </a>
                                                    </li>
                                                @endcan

                                                @can('list product')
                                                    <li class="{{ Request::routeIs('admin.basic.product-other.*') ? 'active' : '' }}">
                                                        <a href="{{ route('admin.basic.product-other.index') }}">
                                                            <i class="fa fa-circle-o"></i>
                                                            <span> Other Products </span>
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                    </li>
                                @endcanany
                                @canany(['list shipping_method', 'add shipping_method'])
                                    <li class="{{ Request::routeIs('admin.basic.shipping_method.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.basic.shipping_method.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Shipping Method </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list bank', 'add bank'])
                                    <li class="{{ Request::routeIs('admin.basic.bank.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.basic.bank.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Bank </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list income_category', 'add income_category'])
                                    <li class="{{ Request::routeIs('admin.basic.income-category.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.basic.income-category.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Income Category </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list expense_category', 'add expense_category'])
                                    <li class="{{ Request::routeIs('admin.basic.expense-category.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.basic.expense-category.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Expense Category </span>
                                        </a>
                                    </li>
                                @endcanany
                            </ul>
                        </li>
                    @endcanany
                    @canany([
                        'list asset', 'add asset', 'list asset-item', 'add asset-item', 'asset ledger'])
                        <li class="treeview {{ Request::routeIs('admin.asset.*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-server"></i>
                                <span>Asset</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                @canany(['asset ledger'])
                                    <li class="{{ Request::routeIs('admin.asset.ledger.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.asset.ledger') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Asset List </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list asset', 'add asset'])
                                    <li class="{{ Request::routeIs('admin.asset.assets.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.asset.assets.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Add Asset </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list asset-item', 'add asset-item'])
                                    <li class="{{ Request::routeIs('admin.asset.asset-items.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.asset.asset-items.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Asset Entry </span>
                                        </a>
                                    </li>
                                @endcanany
                                
                            </ul>
                        </li>
                    @endcanany
                    @canany([
                        'list accessory', 'add accessory', 'list accessory-purchase', 'add accessory-purchase', 'list accessory-purchase_return', 'add accessory-purchase_return', 'list accessory-consume', 'add accessory-consume', 'accessory ledger'])
                        <li class="treeview {{ Request::routeIs('admin.accessory.*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-shopping-bag"></i>
                                <span>Accessories</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                @canany(['accessory ledger'])
                                    <li class="{{ Request::routeIs('admin.accessory.ledger.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.accessory.ledger') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Accessory Report </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list accessory', 'add accessory'])
                                    <li class="{{ Request::routeIs('admin.accessory.accessories.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.accessory.accessories.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span>Accessory</span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list accessory-purchase', 'add accessory-purchase'])
                                    <li class="{{ Request::routeIs('admin.accessory.purchase.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.accessory.purchase.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Purchase Accessory </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list accessory-purchase_return', 'add accessory-purchase_return'])
                                    <li class="{{ Request::routeIs('admin.accessory.purchase_returns.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.accessory.purchase_returns.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Purchase Return Accessory </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list accessory-consume', 'add accessory-consume'])
                                    <li class="{{ Request::routeIs('admin.accessory.consume.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.accessory.consume.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Consume Accessory </span>
                                        </a>
                                    </li>
                                @endcanany
                                
                            </ul>
                        </li>
                    @endcanany
                    

                    @canany(['list role', 'add role'])
                        <li class="{{ Request::routeIs('admin.role.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.role.index') }}">
                                <i class="fa fa-dashboard"></i>
                                <span>Roles</span>
                            </a>
                        </li>
                    @endcanany
                    @canany([
                        'list admin', 
                        'add admin', 
                        'list supplier', 
                        'add supplier' ,
                        'list dyeing-agent', 
                        'add dyeing-agent' , 
                        'list seller', 
                        'add seller', 
                        'list reseller', 
                        'add reseller' , 
                        'list staff', 
                        'add staff', 
                        'list customer', 
                        'add customer', 
                        'list delivery_agent', 
                        'add delivery_agent', 
                        'add customer', 
                        'list investor', 
                        'add investor', 
                        'add customer', 
                        'list loan-holder', 
                        'add loan-holder', 
                        'list reseller_business', 
                        'add reseller_business'
                    ])
                        <li class="treeview {{ Request::routeIs('admin.user.*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-users"></i>
                                <span>Users</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">

                                @can('list admin')
                                    <li class="{{ Request::routeIs('admin.user.admin.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.admin.index') }}"><i
                                                class="fa fa-circle-o"></i>Admins</a>
                                    </li>
                                @endcan
                                @can('list seller')
                                    <li class="{{ Request::routeIs('admin.user.seller.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.seller.index') }}"><i
                                                class="fa fa-circle-o"></i>Sellers</a>
                                    </li>
                                @endcan
                                @can('list reseller')
                                    <li class="{{ Request::routeIs('admin.user.reseller.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.reseller.index') }}"><i
                                                class="fa fa-circle-o"></i>Resellers</a>
                                    </li>
                                @endcan
                                @can('list reseller_business')
                                    <li class="{{ Request::routeIs('admin.user.reseller_business.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.reseller_business.index') }}"><i
                                                class="fa fa-circle-o"></i>Reseller Business</a>
                                    </li>
                                @endcan
                                @can('list staff')
                                    <li class="{{ Request::routeIs('admin.user.staff.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.staff.index') }}"><i
                                                class="fa fa-circle-o"></i>Staffs</a>
                                    </li>
                                @endcan
                                @can('list supplier')
                                    <li class="{{ Request::routeIs('admin.user.supplier.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.supplier.index') }}"><i
                                                class="fa fa-circle-o"></i>Supplier</a>
                                    </li>
                                @endcan
                                @can('list dyeing-agent')
                                    <li class="{{ Request::routeIs('admin.user.dyeing-agent.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.dyeing-agent.index') }}"><i
                                                class="fa fa-circle-o"></i>Dyeing Agent</a>
                                    </li>
                                @endcan
                                @can('list customer')
                                    <li class="{{ Request::routeIs('admin.user.customer.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.customer.index') }}"><i
                                                class="fa fa-circle-o"></i>Customer</a>
                                    </li>
                                @endcan
                                @can('list delivery_agent')
                                    <li class="{{ Request::routeIs('admin.user.delivery_agent.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.delivery_agent.index') }}"><i
                                                class="fa fa-circle-o"></i>Delivery Agent</a>
                                    </li>
                                @endcan
                                @can('list investor')
                                    <li class="{{ Request::routeIs('admin.user.investor.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.investor.index') }}"><i
                                                class="fa fa-circle-o"></i>Investor</a>
                                    </li>
                                @endcan
                                @can('list loan-holder')
                                    <li class="{{ Request::routeIs('admin.user.loan-holder.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.user.loan-holder.index') }}"><i
                                                class="fa fa-circle-o"></i>Loan Holder</a>
                                    </li>
                                @endcan
                               
                            </ul>
                        </li>
                    @endcanany
                    @include('hrm::sidebar')
                    @canany([
                        'list slider', 
                        'add slider', 
                        'list page', 
                        'add page', 
                        'list faq', 
                        'add faq', 
                        'add site-setting' ,
                        'list ecommerce-orders'
                    ])
                        <li class="treeview {{ Request::routeIs('admin.ecommerce.*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-globe" aria-hidden="true"></i>
                                <span>Ecommerce</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                @canany(['list ecommerce-orders'])
                                    <li class="{{ Request::routeIs('admin.ecommerce.orders.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.ecommerce.orders.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Orders </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list slider'])
                                    <li class="{{ Request::routeIs('admin.ecommerce.sliders.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.ecommerce.sliders.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Sliders </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['add site-setting'])
                                    <li class="{{ Request::routeIs('admin.ecommerce.settings.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.ecommerce.settings.create') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Site Settings </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list page', 'add page'])
                                    <li class="{{ Request::routeIs('admin.ecommerce.pages.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.ecommerce.pages.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Pages </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list highlight', 'add highlight'])
                                    <li class="{{ Request::routeIs('admin.ecommerce.highlights.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.ecommerce.highlights.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> Highlights </span>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['list faq', 'add faq'])
                                    <li class="{{ Request::routeIs('admin.ecommerce.faq.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.ecommerce.faq.index') }}">
                                            <i class="fa fa-circle-o"></i>
                                            <span> FAQ </span>
                                        </a>
                                    </li>
                                @endcanany
                                
                            </ul>
                        </li>
                    @endcanany
                    @canany([
                        'raw-material ledger',
                        'finished ledger',
                        'customer ledger',
                        'supplier ledger',
                        'seller ledger',
                        'reseller ledger',
                        'reseller-business ledger',
                        'delivery agent ledger',
                        'expense ledger',
                        'income ledger',
                        'bank ledger',
                        ])
                        <li class="treeview {{ Request::routeIs('admin.report.*') ? 'active menu-open' : '' }}">
                            <a href="#">
                                <i class="fa fa-bars"></i>
                                <span>{{ __('Ledger') }}</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                @canany(['raw-material ledger', 'finished ledger'])
                                    <li class="{{ Request::routeIs('admin.report.product-stock') || Request::routeIs('admin.report.product-ledger') || Request::routeIs('admin.report.finished-product-stock') || Request::routeIs('admin.report.finished-product-ledger') ? 'active menu-open' : '' }}">
                                        <a href="#"><i class="fa fa-circle-o"></i>Stock <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                            <ul class="treeview-menu">
                                                @can('raw-material ledger')
                                                    <li class="{{ Request::routeIs('admin.report.product-stock') || Request::routeIs('admin.report.product-ledger') ? 'active' : '' }}">
                                                        <a href="{{ route('admin.report.product-stock', 'raw-material') }}"> <i class="fa fa-circle-o"></i> Fabric </a>
                                                    </li>
                                                @endcan

                                                @can('finished ledger')
                                                    <li class="{{ Request::routeIs('admin.report.finished-product-stock') || Request::routeIs('admin.report.finished-product-ledger') ? 'active' : '' }}">
                                                        <a href="{{ route('admin.report.finished-product-stock', 'finished') }}"><i class="fa fa-circle-o"></i> Finished</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                    </li>
                                @endcanany
                                @canany([
                                    'customer ledger',
                                    'supplier ledger',
                                    'seller ledger',
                                    'reseller ledger',
                                    'reseller business ledger',
                                    'delivery agent ledger',
                                    'dyeing-agent ledger',
                                    'expense ledger',
                                    'income ledger',
                                    'bank ledger',
                                    ])
                                    <li
                                        class="{{ Request::routeIs('admin.report.supplier') ||
                                        Request::routeIs('admin.report.supplier-transactions') ||
                                        Request::routeIs('admin.report.customer') ||
                                        Request::routeIs('admin.report.customer-transactions') ||
                                        Request::routeIs('admin.report.reseller-business') ||
                                        Request::routeIs('admin.report.reseller-business-transactions') ||
                                        Request::routeIs('admin.report.seller') ||
                                        Request::routeIs('admin.report.seller-transactions') ||
                                        Request::routeIs('admin.report.reseller') ||
                                        Request::routeIs('admin.report.reseller-transactions') ||
                                        Request::routeIs('admin.report.delivery-agent') ||
                                        Request::routeIs('admin.report.delivery-agent-transactions') ||
                                        Request::routeIs('admin.report.dyeing-agent') ||
                                        Request::routeIs('admin.report.dyeing-agent-transactions') ||
                                        Request::routeIs('admin.report.bank') ||
                                        Request::routeIs('admin.report.bank-transactions') ||
                                        Request::routeIs('admin.report.orders') ||
                                        Request::routeIs('admin.report.income') ||
                                        Request::routeIs('admin.report.expense')
                                            ? 'active menu-open'
                                            : '' }}">
                                        <a href="#">
                                            <i class="fa fa-circle-o"></i>
                                            {{ __('Ledger') }}
                                            <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span> </a>
                                        <ul class="treeview-menu">
                                            @can('orders ledger')
                                                <li class="{{ Request::routeIs('admin.report.orders') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.orders') }}"><i
                                                            class="fa fa-circle-o"></i>Orders</a>
                                                </li>
                                            @endcan
                                            @can('supplier ledger')
                                                <li class="{{ Request::routeIs('admin.report.supplier') || Request::routeIs('admin.report.supplier-transactions') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.supplier') }}"><i
                                                            class="fa fa-circle-o"></i>{{ __('Suppliers') }}</a>
                                                </li>
                                            @endcan

                                            @can('customer ledger')
                                                <li
                                                    class="{{ Request::routeIs('admin.report.customer') || Request::routeIs('admin.report.customer-transactions') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.customer') }}"><i
                                                            class="fa fa-circle-o"></i>Customers</a>
                                                </li>
                                            @endcan
                                            @can('seller ledger')
                                                <li
                                                    class="{{ Request::routeIs('admin.report.seller') || Request::routeIs('admin.report.seller-transactions') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.seller') }}"><i
                                                            class="fa fa-circle-o"></i>Sellers</a>
                                                </li>
                                            @endcan
                                            @can('reseller ledger')
                                                <li
                                                    class="{{ Request::routeIs('admin.report.reseller') || Request::routeIs('admin.report.reseller-transactions') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.reseller') }}"><i
                                                            class="fa fa-circle-o"></i>Resellers</a>
                                                </li>
                                            @endcan
                                            @can('reseller business ledger')
                                                <li
                                                    class="{{ Request::routeIs('admin.report.reseller-business') || Request::routeIs('admin.report.reseller-business-transactions') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.reseller-business') }}"><i
                                                            class="fa fa-circle-o"></i>Reseller business</a>
                                                </li>
                                            @endcan
                                            @can('delivery agent ledger')
                                                <li
                                                    class="{{ Request::routeIs('admin.report.delivery-agent') || Request::routeIs('admin.report.delivery-agent-transactions') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.delivery-agent') }}"><i
                                                            class="fa fa-circle-o"></i>Delivery Agent</a>
                                                </li>
                                            @endcan

                                            @can('dyeing-agent ledger')
                                                <li
                                                    class="{{ Request::routeIs('admin.report.dyeing-agent') || Request::routeIs('admin.report.dyeing-agent-transactions') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.dyeing-agent') }}"><i
                                                            class="fa fa-circle-o"></i>Dyeing Agent</a>
                                                </li>
                                            @endcan

                                            @can('income ledger')
                                                <li class="{{ Request::routeIs('admin.report.income') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.income') }}"><i
                                                            class="fa fa-circle-o"></i>{{ __('Incomes') }}</a>
                                                </li>
                                            @endcan

                                            @can('expense ledger')
                                                <li class="{{ Request::routeIs('admin.report.expense') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.expense') }}"><i
                                                            class="fa fa-circle-o"></i>Expenses</a>
                                                </li>
                                            @endcan

                                            @can('bank ledger')
                                                <li
                                                    class="{{ Request::routeIs('admin.report.bank') || Request::routeIs('admin.report.bank-transactions') ? 'active' : '' }}">
                                                    <a href="{{ route('admin.report.bank') }}"><i
                                                            class="fa fa-circle-o"></i>Banks</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany  
                                @canany(['income statement ledger', 'balance sheet ledger'])
                                    <li
                                        class="{{ Request::routeIs('admin.report.income-statement') ||  Request::routeIs('admin.report.balance-sheet') ? 'active menu-open' : '' }}">
                                        <a href="#"><i class="fa fa-circle-o"></i>Report <span
                                                class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                            <ul class="treeview-menu">
                                                @can('income statement ledger')
                                                    <li
                                                        class="{{ Request::routeIs('admin.report.income-statement') ? 'active' : '' }}">
                                                        <a href="{{ route('admin.report.income-statement') }}"> <i
                                                                class="fa fa-circle-o"></i> Income Statement</a>
                                                    </li>
                                                @endcan

                                                @can('balance sheet ledger')
                                                    <li
                                                        class="{{ Request::routeIs('admin.report.balance-sheet') ? 'active' : '' }}">
                                                        <a href="{{ route('admin.report.balance-sheet') }}"><i
                                                                class="fa fa-circle-o"></i> Balance Sheet</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                    </li>
                                @endcanany
                        </li>
                    @endcanany
                </ul>
            </section>
        </aside>

        <div class="content-wrapper">
            @if (session('successMessage'))
                <section class="content-header">
                    <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        {!! session('successMessage') !!}
                    </div>
                </section>
            @endif

            @if (session('errorMessage'))
                <section class="content-header">
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        {!! session('errorMessage') !!}
                    </div>
                </section>
            @endif

            @yield('content')
        </div>

        <footer class="main-footer hidden-print">
            <div class="pull-right hidden-xs">
                Developed by <a href="https://oshnisoftware.com" target="_blank">OSHNI SOFTWARE</a>
            </div>
            <strong>
                Copyright &copy; {{ date('Y') }} {{ 'OSHNI SOFTWARE' }}.
            </strong> All rights reserved.
        </footer>
    </div>

    <script src="{{ asset('admin-assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/jquery/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/fancybox-3.0/jquery.fancybox.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/app.min.js') }}"></script>
    <script>
        var base_url = '{{ url('') }}';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
