@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>خدمات رفاهی / کیف پول تارا

                    </h5>
                </div>

            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-body"><h6 class="mb-4">اعتبار تارا</h6>
                    <div class="row d-flex align-items-center">
                        <div class="col-9"><h4 class="f-w-300 d-flex align-items-center m-b-0"><i
                                        class="feather icon-zap f-30 text-success"></i> {{number_format($balance)}} ریال</h4></div>

                    </div>
                    <div class="progress m-t-30" style="height: 7px">
                        <div class="progress-bar bg-brand-color-1" role="progressbar" style="width: 100%"
                             aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-body"><h6 class="mb-4">خرید از تارا</h6>
                    <div class="row d-flex align-items-center">

                        <div class="col-9">

                            <h4 class="f-w-300 d-flex align-items-center m-b-0">
                                <a href="{{route("accounting.welfare_service.tara.client.dashboard.purchase_key")}}">
                                <i
                                        class="fas fa-shopping-basket text-primary f-30"></i>

                                دریافت بارکد خرید حضوری
                                </a>
                            </h4>
                        </div>

                    </div>
                    <div class="progress m-t-30" style="height: 7px">
                        <div class="progress-bar bg-brand-color-1" role="progressbar" style="width: 100%"
                             aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

{{--        <div class="col-md-6 col-xl-4">--}}
{{--            <div class="card">--}}
{{--                <div class="card-body"><h6 class="mb-4">تراکنش ها</h6>--}}
{{--                    <div class="row d-flex align-items-center">--}}

{{--                        <div class="col-9">--}}

{{--                            <h4 class="f-w-300 d-flex align-items-center m-b-0">--}}
{{--                                <a href="{{route("accounting.welfare_service.tara.client.dashboard.transactions")}}">--}}
{{--                                    <i--}}
{{--                                            class="fas fa-clock text-primary f-30"></i>--}}

{{--                                    مشاهده صورت حساب--}}
{{--                                </a>--}}
{{--                            </h4>--}}
{{--                        </div>--}}

{{--                    </div>--}}
{{--                    <div class="progress m-t-30" style="height: 7px">--}}
{{--                        <div class="progress-bar bg-brand-color-1" role="progressbar" style="width: 100%"--}}
{{--                             aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
