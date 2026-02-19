@extends('layouts.admin._master')
@section("page_header_title","داشبورد پیمانکاران -  ".$contractor->fullCaption())

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دستور پیمان {{$contractor_allocation->production->serial()}}</h5>
                </div>
                <div class="card-block">

                    @include("contractor.panel.dashboard._info_small")

                    <div class="col-md-12">
                        <a href="{{route("contractor.panel.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        @if( $contractor_allocation->status_id == 5310107)
                            <a href="{{route("contractor.panel.coordination_for_sending.index",$contractor_allocation)}}"
                               class="btn btn-primary"> هماهنگی دریافت مواد اولیه </a>
                        @endif

                        @if( in_array($contractor_allocation->status_id,[5310109, 5310104]))
                            <a href="{{route("contractor.panel.register_production.index",$contractor_allocation)}}"
                               class="btn btn-primary"> ثبت تولید </a>
                        @endif

                        @if( $contractor_allocation->packing_forms()->where("status_id",7007008)->count() >0)
                            <a href="{{route("contractor.panel.send_to_employer.index",$contractor_allocation)}}"
                               class="btn btn-primary"> ارسال محصول به کارفرما </a>
                        @endif
                        <a class="btn btn-primary"
                           href="{{route("contractor.panel.print.allocation_card",$contractor_allocation->id)}}">پرینت
                            کارت دستور پیمان </a>

                    </div>
                </div>
            </div>
        </div>

        @include("utility.transport.public._transport_list",[
            "route_downlaod_dcbl"=>"contractor.panel.print.download_transport_card",
            "header_caption"=>"لیست بارهای ارسال شده برای کارفرما"
        ])
        @include("contractor.panel.dashboard._input_form_list")
        @include("contractor.panel.dashboard._packing_form_list")
        @include("contractor.panel.dashboard._form_list")
        @include("contractor.public._log_list")


    </div>

@endsection

@section("scripts")
    <script>

    </script>
@endsection
