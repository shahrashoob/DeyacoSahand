@extends('layouts.admin._master')
@section("page_header_title")
    داشبورد لحظه ای


    &nbsp;
    &nbsp;
    <div class="dropdown drp-user show" style="display: inline">


        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
            <i class="icon feather icon-settings"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right profile-notification " style="width: 350px; font-size: 14px">
            <form id="form1" action="{{route("report.real_time.setting.submit",["type"=>1])}}" method="post"
                  novalidate="novalidate">
                @csrf
                <div style="padding: 15px">
                    <div class="">
                        <input name="allow_show_bar_weigh_in_real_time_dashboard"
                               {{$setting_data["allow_show_bar_weigh_in_real_time_dashboard"]?"checked":""}} type="checkbox">
                        مقدار سفارش ها و کالای تولید شده - تن
                        <br/>

                        <input name="allow_show_bar_price_in_real_time_dashboard"
                               {{$setting_data["allow_show_bar_price_in_real_time_dashboard"]?"checked":""}} type="checkbox">
                        مقدار سفارش ها و کالای تولید شده - میلیارد ریال
                        <br/>

                        <input name="allow_show_line_weight_in_real_time_dashboard"
                               {{$setting_data["allow_show_line_weight_in_real_time_dashboard"]?"checked":""}} type="checkbox">
                        روند فروش وزنی - تن
                        <br/>

                        <input name="allow_show_line_price_in_real_time_dashboard"
                               {{$setting_data["allow_show_line_price_in_real_time_dashboard"]?"checked":""}} type="checkbox">
                        روند فروش - میلیارد ریال
                        <br/>

                        <input name="real_time_top_order_delay_show"
                               {{$setting_data["real_time_top_order_delay_show"]?"checked":""}} type="checkbox">
                        سفارشات دارای بیشترین تاخیر
                        <br/>

                        <input name="real_time_top_customer_show"
                               {{$setting_data["real_time_top_customer_show"]?"checked":""}} type="checkbox">
                        مشتریان برتر که بیشترین خرید را داشته اند
                        <br/>

                        <input name="allow_show_polar_in_real_time_dashboard"
                               {{$setting_data["allow_show_polar_in_real_time_dashboard"]?"checked":""}} type="checkbox">
                        نمودار قطبی برنامه ریزی
                        <br/>
                        <button type="submit" class="btn btn-primary btn-sm">ثبت</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@php
    if(!function_exists("to_persian")){
        function to_persian($string) {
            $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

            $output= str_replace($english,$persian,  $string);
            return $output;
        }
			function to_money($number,$round=2) {
// تبدیل به عدد اعشاری مطمئن
            $number = floatval($number);

            // اگر عدد اعشار ندارد
            if (fmod($number, 1) == 0) {
                $formatted = number_format($number, 0, '٫', '٬');
            } else {
                // اگر اعشار دارد
                // حذف صفرهای اضافی انتهای اعشار
                $formatted = rtrim(rtrim(number_format($number, $round, '٫', '٬'), '۰'), '٫');
            }
            return to_persian($formatted);
        }
    }

@endphp
@section("content")
    <div class="row">

        @if($allow_show_bar_weigh_in_real_time_dashboard)
            <div class="col-md-6 center ">
                <div class="card card2">
                    <h5 class="h5_chart  ">

                        مقدار سفارش ها و کالای تولید شده
                        - تن
                    </h5>


                    <div id="real_time_show_report_bar_amount"
                         style="width: 300px; height: 300px;margin: auto;margin-top: -30px; overflow: auto ">

                    </div>

                </div>
            </div>
            <script>
                var dom = document.getElementById('real_time_show_report_bar_amount');

                var myChart = echarts.init(dom);
                var app = {};
                var app = {};
                var app = {};
                var option;


                option = {
                    tooltip: {
                        trigger: 'axis'
                    },
                    toolbox: {
                        show: false,
                        feature: {
                            mark: {
                                show: true
                            },
                            dataView: {
                                show: true,
                                readOnly: false
                            },
                            magicType: {
                                show: true,
                                type: [ 'bar', 'stack', 'tiled']
                            },
                            restore: {
                                show: true
                            },
                            saveAsImage: {
                                show: true
                            }
                        }
                    },
                    calculable: true,
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        data: ['کل سفارش ها', 'کارت صادر شده', 'مقدار تولید شده', 'در انتظار تولید', 'مقدار ارسال شده'],
                        axisLabel: {interval: 0, rotate: 40, distance: 300},

                    },
                    color: ['#8C51F7',"#e26060"],
                    yAxis: [{
                        type: 'value',
                        splitLine: {
                            show: false
                        }
                    }],
                    series: [
                        {
                            type: 'bar', label: {
                                show: true,
                                position: 'top',
                                valueAnimation: true,
                                color: "black"
                            }, data: [],
                        },
                        {
                            name: '',
                            type: 'bar', stack: 'one',
                            smooth: true,
                            itemStyle: {
                                normal: {
                                    areaStyle: {
                                        type: 'macarons'
                                    }
                                }
                            },
                            data: ["-", "-", "-", {{max($waiting_production- $order_report["waiting_production"],0)}}, "-"],

                        },
                        {
                            name: '',
                            type: 'bar',
                            smooth: true, stack: 'one',
                            itemStyle: {
                                normal: {
                                    areaStyle: {
                                        type: 'macarons'
                                    }
                                }
                            },label: {
                                show: true,
                                position: 'top',
                                valueAnimation: true,
                                color: "black"
                            },
                            data: [{{$order_report["sum_order_all"]}}, {{$order_report["production_amount"]}}, {{$order_report["production_form_amount"]}}, {{$order_report["waiting_production"]}}, {{$order_report["sum_amount_sent"]}}],

                        },
                    ],
                    textStyle: {
                        fontFamily: 'IranSans'
                    }
                };
                myChart.setOption(option, true);
            </script>
        @endif

        @if($allow_show_bar_price_in_real_time_dashboard)
            <div class="col-md-6 center ">
                <div class="card card2">
                    <h5 class="h5_chart">
                        مقدار سفارش ها و کالای تولید شده
                        - میلیارد ریال
                    </h5>


                    <div id="real_time_show_report_bar_price"
                         style="width: 300px; height: 300px;margin: auto;margin-top: -30px; overflow: auto ">

                    </div>

                </div>
            </div>
            <script>
                var dom = document.getElementById('real_time_show_report_bar_price');

                var myChart = echarts.init(dom);
                var app = {};
                var app = {};
                var app = {};
                var option;

                option = {
                    legend: {},
                    tooltip: {},

                    color: ['#8C51F7'],
                    xAxis: {
                        type: 'category',
                        data: ['کل سفارش ها', 'کارت صادر شده', 'مقدار تولید شده', 'در انتظار تولید', 'مقدار ارسال شده'],
                        axisLabel: {interval: 0, rotate: 40, distance: 300},

                    },
                    yAxis: {},
                    // Declare several bar series, each will be mapped
                    // to a column of dataset.source by default.
                    series: [{
                        type: 'bar', label: {
                            show: true,
                            position: 'top',
                            valueAnimation: true,
                            color: "black"
                        }, data: [],
                    },
                        {
                            type: 'bar', label: {
                                show: true,
                                position: 'top',
                                valueAnimation: true,
                                color: "black"
                            },
                            data: [{{$order_report["sum_order_all_price"]}}, {{$order_report["production_amount_price"]}}, {{$order_report["production_form_amount_price"]}}, {{$order_report["waiting_production_price"]}}, {{$order_report["sum_amount_sent_price"]}}],

                        }
                    ],
                    textStyle: {
                        fontFamily: 'IranSans'
                    }
                };
                myChart.setOption(option, true);
            </script>
        @endif

        @if($allow_show_line_weight_in_real_time_dashboard)
            <div class="col-md-6 center ">
                <div class="card card2">
                    <h5 class="h5_chart">
                        روند فروش وزنی
                        - تن
                    </h5>


                    <div id="real_time_show_weight"
                         style="width: 300px; height: 300px;margin: auto;margin-top: -30px; overflow: auto ">

                    </div>

                </div>
            </div>
            <script>
                var dom = document.getElementById('real_time_show_weight');

                var myChart = echarts.init(dom);
                var app = {};
                var app = {};
                var app = {};
                var option;

                option = {
                    legend: {},
                    tooltip: {},

                    color: ['#8C51F7'],
                    xAxis: {
                        type: 'category',
                        data: [@foreach($order_report_line["date_number"] as $item) '{{$item}}', @endforeach],
                        axisLabel: {interval: 0, rotate: 40, distance: 300},

                    },
                    yAxis: {},
                    // Declare several bar series, each will be mapped
                    // to a column of dataset.source by default.
                    series: [{
                        type: 'line',
                        smooth: true,
                        label: {
                            show: true,
                            position: 'top',
                            valueAnimation: true,
                            color: "black"
                        },
                        data: [@foreach($order_report_line["weight_list"] as $item) '{{$item}}', @endforeach],
                    }],
                    textStyle: {
                        fontFamily: 'IranSans'
                    }
                };
                myChart.setOption(option, true);
            </script>
        @endif

        @if($allow_show_line_price_in_real_time_dashboard)
            <div class="col-md-6 col-sm-10 center ">
                <div class="card card2">
                    <h5 class="h5_chart">
                        روند فروش
                        - میلیارد ریال
                    </h5>


                    <div id="real_time_show_price"
                         style="width: 300px; height: 300px;margin: auto;margin-top: -30px; overflow: auto ">

                    </div>

                </div>
            </div>
            <script>
                var dom = document.getElementById('real_time_show_price');

                var myChart = echarts.init(dom);
                var app = {};
                var app = {};
                var app = {};
                var option;

                option = {
                    legend: {},
                    tooltip: {},

                    color: ['#8C51F7'],
                    xAxis: {
                        type: 'category',
                        data: [@foreach($order_report_line["date_number"] as $item) '{{$item}}', @endforeach],
                        axisLabel: {interval: 0, rotate: 40, distance: 300},

                    },
                    yAxis: {},
                    // Declare several bar series, each will be mapped
                    // to a column of dataset.source by default.
                    series: [{
                        type: 'line',
                        smooth: true, label: {
                            show: true,
                            position: 'top',
                            valueAnimation: true,
                            color: "black"
                        },
                        data: [@foreach($order_report_line["price_list"] as $item) '{{$item}}', @endforeach],
                    }],
                    textStyle: {
                        fontFamily: 'IranSans'
                    }
                };
                myChart.setOption(option, true);
            </script>
        @endif

        @if($allow_show_polar_in_real_time_dashboard)
            <div class="col-md-12 col-sm-10 center ">
                <div class="card card2">
                    <h5 class="h5_chart">
                        نمودار قطبی -
                    </h5>


                    <div id="polar_bar"
                         style="width: 900px; height: 900px;margin: auto; overflow: auto ">

                    </div>

                </div>
            </div>
            <script>
                var dom = document.getElementById('polar_bar');

                var myChart = echarts.init(dom);
                var app = {};
                var app = {};
                var app = {};
                var option;

                option = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
                    legend: {},
                    xAxis: {
                        type: 'value',
                        boundaryGap: [0, 0.01]
                    },
                    yAxis: {
                        type: 'category',
                        data: [  'موجودی', 'China', 'مقدار باقی مانده سفارش','کل سفارش ها']
                    },
                    series: [

                        {

                            type: 'bar',
                            data: [ {{$inventory_all}}, 0, {{$order_remaining}},{{$order_report["sum_order_all"]}}]
                        }
                    ]
                };
                myChart.setOption(option, true);
            </script>
        @endif

        <div class="w-100"><br/>
            <br/></div>

        @if($real_time_top_customer_show)
            <div class="col-md-6 ">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            {{$real_time_top_customer_number}}
                            مشتری برتر که بیشترین خرید را در {{$real_time_top_customer_days}}
                            روز گذشته داشته اند
                        </h5>

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            <i class="icon feather icon-settings"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-notification " style="width: 350px; font-size: 14px">
                            <form id="form1" action="{{route("report.real_time.setting.submit",["type"=>2])}}" method="post"
                                  novalidate="novalidate">
                                @csrf
                                <div style="padding: 15px; text-align: right">
                                    <div class="">

                                        تعداد روزهای بررسی سفارشات
                                        <input name="real_time_top_customer_days"
                                                type="text"  style="width: 60px" value="{{$setting_data["real_time_top_customer_days"]}}"> روز
                                        <br/>

                                        تعداد مشتریان برتر
                                        <input name="real_time_top_customer_number"
                                               type="text"  style="width: 60px" value="{{$setting_data["real_time_top_customer_number"]}}"> روز
                                        <br/>


                                        <button type="submit" class="btn btn-primary btn-sm">ثبت</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="card-block" style="overflow: auto">


                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>نام مشتری</th>
                                <th> سفارش - تن</th>
                                <th>سفارش (میلیارد ریال)</th>
                                <th> خرید - تن</th>
                                <th>خرید (میلیارد ریال)</th>
                            </tr>
                            @php $row=1;@endphp
                            @foreach($top_customer as $customer)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("sales.product_request_permission.index",[$customer->order_id,"real_time"])}}"> {{$customer->customer->caption}}</i>
                                        </a>
                                    </td>
                                    <td>{{to_money($customer->sum_amount/1000)}}</td>
                                    <td>{{to_money(round($customer->sum_price/1000000000,2))}} </td>
                                    <td>{{to_money($customer->sum_amount_buy/1000)}}</td>
                                    <td>{{to_money(round($customer->sum_price_buy/1000000000,2))}} </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endif
        @if($real_time_top_order_delay_show)
            <div class="col-md-6 ">
                <div class="card">
                    <div class="card-header">
                        <h5>
                            سفارشات دارای بیشترین تاخیر
                        </h5>

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            <i class="icon feather icon-settings"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-notification " style="width: 350px; font-size: 14px">
                            <form id="form1" action="{{route("report.real_time.setting.submit",["type"=>2])}}" method="post"
                                  novalidate="novalidate">
                                @csrf
                                <div style="padding: 15px; text-align: right">
                                    <div class="">

                                        تعداد نمایش سفارشات با بیشترین تاخیر
                                        <input name="real_time_top_order_delay_number"
                                               type="text"  style="width: 60px" value="{{$setting_data["real_time_top_order_delay_number"]}}"> روز
                                        <br/>


                                        <button type="submit" class="btn btn-primary btn-sm">ثبت</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="card-block" style="overflow: auto">


                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>شماره سفارش</th>
                                <th>نام مشتری</th>
                                <th>درصد ارسال</th>
                                <th>تعداد روز در انتظار ارسال</th>
                            </tr>
                            @php $row=1;@endphp
                            @foreach($waiting_order["order"] as $order)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("sales.dashboard.view_order",[$order])}}">{{$order->code()}}</i>
                                        </a>

                                    </td>
                                    <td>{{$order->customer->caption}}</td>

                                    <td>
                                        @if(isset($waiting_order["amount_of_sent"][$order->id]))
                                            {{$waiting_order["amount_of_sent"][$order->id]}} %
                                        @else
                                            0 %
                                        @endif
                                    </td>
                                    <td>{{$order->number_of_days_waiting()}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-12 center">
            <a href="{{route("report.cross_sectional_management.dashboard.index")}}"
               class="btn btn-outline-dark">بازگشت</a>
        </div>

    </div>

@endsection
@section("styles")

    <script src="{{asset('assets/plugins/chart-echarts/js/echarts-en.min.js')}}"></script>
    <style>
        .h5_chart {
            font-size: 14px;
            font-weight: bold;
            margin-right: 40px;
            background: #4EE7BD;
            padding: 10px;
            border-radius: 30px;
            margin: auto;
            margin-top: 15px;
        }

        .card2 {
            overflow: auto;
            padding: 10px;
            height: 400px;
        }
    </style>
@endsection
@section("scripts")


@endsection



