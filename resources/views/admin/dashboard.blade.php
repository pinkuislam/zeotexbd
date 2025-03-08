@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <style>
        .small-box-footer {
            border-radius: 0px 0px 35px 35px !important;
        }

        .dashboardColor {
            color: white;
            font-weight: bold;
            border-radius: 20px
        }
        .pos_tab_btn ul li a {
            color: #415094;
            font-size: 12px;
            font-weight: 500;
            background: #fff;
            border-radius: 30px;
            display: inline-block;
            transition: .3s;
            padding: 7px 23px;
            white-space: nowrap;
            border: 1px solid #3cd6d3;
            text-transform: uppercase;
            line-height: 16px;
        }
        .pos_tab_btn .nav-pills>li.active>a {
            color: #fff;
            background: #3cd6d3 !important;
            border-color: transparent;
        }
        .pos_tab_btn ul li a:hover {
            color: #fff;
            background: #3cd6d3;
            border-color: transparent;
        }
    </style>

    <section class="content">
       
        <div class="pos_tab_btn">

            <div style="display: flex; justify-content:space-between; align-items:center; flex-wrap: wrap">
                <a style=" display:inline-block" href="{{route('admin.pending.sale')}}"> Ordered  <i class="fa fa-bell fa-2x"></i> 
                    <span style="width:60px; height:50px; border-radius: 50%; background:#28a745  ; padding:3px 2px; color:#fff; position:relative; top:-17px;left:-18px ">
                        {{$pending_fourDays_totalOrders}} 
                    </span>
                </a>
                <a style=" display:inline-block" href="{{route('admin.sale.pending.delivery')}}"> Processing <i class="fa fa-bell fa-2x"></i> 
                    <span style="width:60px; height:50px; border-radius: 50%; background:#ffc107 ; padding:3px 2px; color:#fff; position:relative; top:-17px;left:-18px ">
                        {{$processing_fourDays_totalSales}}
                    </span>
                </a>
                <a style=" display:inline-block" href="{{route('admin.sale.sale.delivered')}}"> Delivered <i class="fa fa-bell fa-2x"></i> 
                    <span style="width:60px; height:50px; border-radius: 50%; background:#dc3545 ; padding:3px 2px; color:#fff; position:relative; top:-17px;left:-18px ">
                        {{$delivered_totalSales}}
                    </span>
                </a>

                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" style="display:flex; justify-content:flex-end;flex-wrap: wrap; margin-bottom:20px">
                    <li class="nav-item active">
                        <a class="nav-link filtering" data-type="today" id="totay-tab" data-toggle="pill" href="#totay" role="tab" aria-controls="totay" aria-selected="true">Today</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link filtering" data-type="week" id="week-tab" data-toggle="pill" href="#week" role="tab" aria-controls="week" aria-selected="false">This Week</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link filtering" data-type="monty" id="monty-tab" data-toggle="pill" href="#monty" role="tab" aria-controls="monty" aria-selected="false">This Month</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link filtering" data-type="year" id="year-tab" data-toggle="pill" href="#year" role="tab" aria-controls="year" aria-selected="false">This Year</a>
                    </li>
                </ul>
            </div>
           
           
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade active in" id="totay" role="tabpanel" aria-labelledby="totay-tab"> 
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="dashboardColor small-box bg-info" style="background-color: #17a2b8 !important;">
                                <div class="inner">
                                    <h3>{{ $today_totalOrders  }}</h3>
                                    <p>Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="dashboardColor small-box bg-info" style="background-color: #28a745 !important;">
                                <div class="inner">
                                    <h3>{{ $ordered_today_totalOrders  }}</h3>
                                    <p>Ordered Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="dashboardColor small-box bg-info" style="background-color: #ffc107 !important;">
                                <div class="inner">
                                    <h3>{{ $processing_today_totalOrders  }}</h3>
                                    <p>Processing Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="dashboardColor small-box bg-info" style="background-color: #dc3545 !important;">
                                <div class="inner">
                                    <h3>{{ $delivered_today_totalOrders  }}</h3>
                                    <p>Delivered Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="week" role="tabpanel" aria-labelledby="week-tab"> 
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-info" style="background-color: #17a2b8 !important;">
                                <div class="inner">
                                    <h3>{{ $week_totalOrders  }}</h3>
                                    <p>Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-success" style="background-color: #28a745 !important;">
                                <div class="inner">
                                    <h3>{{ $ordered_week_totalOrders  }}</h3>
                                    <p>Ordered Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-warning" style="background-color: #ffc107 !important;">
                                <div class="inner">
                                    <h3>{{ $processing_week_totalOrders  }}</h3>
                                    <p>Processing Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-danger" style="background-color: #dc3545 !important;">
                                <div class="inner">
                                    <h3>{{ $delivered_week_totalOrders  }}</h3>
                                    <p>Delivered Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                
                            </div>
                        </div>
                    <!-- ./col -->
                    </div>
                </div>
                <div class="tab-pane fade" id="monty" role="tabpanel" aria-labelledby="monty-tab"> 
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-info" style="background-color: #17a2b8 !important;">
                                <div class="inner">
                                    <h3>{{ $month_totalOrders  }}</h3>
                                    <p>Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-success" style="background-color: #28a745 !important;">
                                <div class="inner">
                                    <h3>{{ $ordered_month_totalOrders  }}</h3>
                                    <p>Ordered Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-warning" style="background-color: #ffc107 !important;">
                                <div class="inner">
                                    <h3>{{ $processing_month_totalOrders  }}</h3>
                                    <p>Processing Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-danger" style="background-color: #dc3545 !important;">
                                <div class="inner">
                                    <h3>{{ $delivered_month_totalOrders  }}</h3>
                                    <p>Delivered Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>
                </div>
                <div class="tab-pane fade" id="year" role="tabpanel" aria-labelledby="year-tab"> 
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-info" style="background-color: #17a2b8 !important;">
                                <div class="inner">
                                    <h3>{{ $year_totalOrders  }}</h3>
                                    <p> Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-success" style="background-color: #28a745 !important;">
                                <div class="inner">
                                    <h3>{{ $ordered_year_totalOrders  }}</h3>
                                    <p>Ordered Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-warning" style="background-color: #ffc107 !important;">
                                <div class="inner">
                                    <h3>{{ $processing_year_totalOrders  }}</h3>
                                    <p>Processing Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="dashboardColor small-box bg-danger" style="background-color: #dc3545 !important;">
                                <div class="inner">
                                    <h3>{{ $delivered_year_totalOrders  }}</h3>
                                    <p>Delivered Total Order</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                
                            </div>
                        </div>
                        <!-- ./col -->
                    </div>
                </div>
            </div>
        </div>
        @if (auth()->user()->role == "Admin")
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="dashboardColor small-box bg-info" style="background-color: #17a2b8 !important;">
                        <div class="inner">
                            <h3>{{ $totalSeller }}</h3>
                            <p>Total Seller</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="dashboardColor small-box bg-info" style="background-color: #28a745 !important;">
                        <div class="inner">
                            <h3>{{ number_format($totalSeller_orderAmount,2)  }}</h3>
                            <p>Total Seller Order Amount</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="dashboardColor small-box bg-info" style="background-color: #ffc107 !important;">
                        <div class="inner">
                            <h3>{{ $totalReseller  }}</h3>
                            <p>Total Reseller</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="dashboardColor small-box bg-info" style="background-color: #dc3545 !important;">
                        <div class="inner">
                            <h3>{{  number_format($totalReseller_orderAmount,2) }}</h3>
                            <p> Total Reseller Order Amount </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        
                    </div>
                </div>
            </div>
        @endif
        <div class="row mt-3">
            <div class="col-md-6 col-xs-12">
                <div class="box box-default" style="border-top-color:#3cd6d3 !important">
                    <div class="box-header with-border">
                        <h3 class="box-title">Top Selling Products</h3>
                    </div>
    
                    <div class="box-body">
                        <ul class="products-list product-list-in-box">
                            @foreach ($topSellingProducts as $item)
                                <li class="item">
                                    <div class="product-info">
                                        <span class="product-title">
                                            {{ $item->product->code ?? '' }}
                                            <span class="label label-success pull-right">{{ $item->product_count }}</span>
                                        </span>
                                        <span class="product-description">
                                            {{ $item->product->name ?? '' }} - {{ $item->color->name ?? '' }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
    
            <div class="col-md-6 col-xs-12">
                <div class="box box-default" style="border-top-color:#3cd6d3 !important">
                    <div class="box-header with-border">
                        <h3 class="box-title">Low Selling Products</h3>
                    </div>
    
                    <div class="box-body">
                        <ul class="products-list product-list-in-box">
                            @foreach ($lowSellingProducts as $item)
                                <li class="item">
                                    <div class="product-info">
                                        <span class="product-title">
                                            {{ $item->product->code?? '' }}
                                            <span class="label label-warning pull-right">{{ $item->product_count }}</span>
                                        </span>
                                        <span class="product-description">
                                            {{ $item->product->name?? '' }} - {{ $item->color->name ?? '' }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-12" style="background:#fff; padding:0">
                    <div id="monthly_charts" style="width:100%; height: 400px;"></div>
                </div>
            </div>
        </div>
        {{-- <div class="row">
            <div class="col-md-12">
                <div class="col-md-12" style="background:#fff; padding:0">
                    <div id="piecontainer" style="width:100%; height: 400px;"></div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="row">
            <div class="col-md-12">
                <div class="col-md-12" style="background:#fff; padding:0">
                    <div id="splinecontainer" style="width:100%; height: 400px;"></div>
                </div>
            </div>
        </div> --}}
    </section>
    @endsection
    @push('scripts')
    <script src="{{ asset('admin-assets/plugins/highcharts/highcharts.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/highcharts/exporting.js') }}"></script>
    <script type="text/javascript">
    
    Highcharts.chart('monthly_charts', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'This Year Monthly Order Amount Chart'
        },
        subtitle: {
            text: '{{ date('Y') }}'
        },
        xAxis: {
            categories: [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Amount'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [
            @foreach ($monthlyOrderCharts as $s => $item)
            {
                name: '{{ $s }}',
                data: [{{ implode(',', $item) }}]
            },
            @endforeach
        ]
    });
            Highcharts.chart('piecontainer', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Egg Yolk Composition'
            },
            tooltip: {
                valueSuffix: '%'
            },
            subtitle: {
                text:
                'Source:<a href="https://www.mdpi.com/2072-6643/11/3/684/htm" target="_default">MDPI</a>'
            },
            plotOptions: {
                series: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: [{
                        enabled: true,
                        distance: 20
                    }, {
                        enabled: true,
                        distance: -40,
                        format: '{point.percentage:.1f}%',
                        style: {
                            fontSize: '1.2em',
                            textOutline: 'none',
                            opacity: 0.7
                        },
                        filter: {
                            operator: '>',
                            property: 'percentage',
                            value: 10
                        }
                    }]
                }
            },
            series: [
                {
                    name: 'Percentage',
                    colorByPoint: true,
                    data: [
                        {
                            name: 'Water',
                            y: 55.02
                        },
                        {
                            name: 'Fat',
                            sliced: true,
                            selected: true,
                            y: 26.71
                        },
                        {
                            name: 'Carbohydrates',
                            y: 1.09
                        },
                        {
                            name: 'Protein',
                            y: 15.5
                        },
                        {
                            name: 'Ash',
                            y: 1.68
                        }
                    ]
                }
            ]
        });

        // On chart load, start an interval that adds points to the chart and animate
        // the pulsating marker.
        const onChartLoad = function () {
            const chart = this,
                series = chart.series[0];

            setInterval(function () {
                const x = (new Date()).getTime(), // current time
                    y = Math.random();

                series.addPoint([x, y], true, true);
            }, 1000);
        };

        // Create the initial data
        const data = (function () {
            const data = [];
            const time = new Date().getTime();

            for (let i = -19; i <= 0; i += 1) {
                data.push({
                    x: time + i * 1000,
                    y: Math.random()
                });
            }
            return data;
        }());

        // Plugin to add a pulsating marker on add point
        Highcharts.addEvent(Highcharts.Series, 'addPoint', e => {
            const point = e.point,
                series = e.target;

            if (!series.pulse) {
                series.pulse = series.chart.renderer.circle()
                    .add(series.markerGroup);
            }

            setTimeout(() => {
                series.pulse
                    .attr({
                        x: series.xAxis.toPixels(point.x, true),
                        y: series.yAxis.toPixels(point.y, true),
                        r: series.options.marker.radius,
                        opacity: 1,
                        fill: series.color
                    })
                    .animate({
                        r: 20,
                        opacity: 0
                    }, {
                        duration: 1000
                    });
            }, 1);
        });


    Highcharts.chart('splinecontainer', {
        chart: {
            type: 'spline',
            events: {
                load: onChartLoad
            }
        },

        time: {
            useUTC: false
        },

        title: {
            text: 'Live random data'
        },

        accessibility: {
            announceNewData: {
                enabled: true,
                minAnnounceInterval: 15000,
                announcementFormatter: function (allSeries, newSeries, newPoint) {
                    if (newPoint) {
                        return 'New point added. Value: ' + newPoint.y;
                    }
                    return false;
                }
            }
        },

        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150,
            maxPadding: 0.1
        },

        yAxis: {
            title: {
                text: 'Value'
            },
            plotLines: [
                {
                    value: 0,
                    width: 1,
                    color: '#808080'
                }
            ]
        },

        tooltip: {
            headerFormat: '<b>{series.name}</b><br/>',
            pointFormat: '{point.x:%Y-%m-%d %H:%M:%S}<br/>{point.y:.2f}'
        },

        legend: {
            enabled: false
        },

        exporting: {
            enabled: false
        },

        series: [
            {
                name: 'Random data',
                lineWidth: 2,
                color: Highcharts.getOptions().colors[2],
                data
            }
        ]
    });

    </script>
    @endpush