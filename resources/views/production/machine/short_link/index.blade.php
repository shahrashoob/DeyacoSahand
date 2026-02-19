@extends('layouts.admin._master')
@section("page_header_title","داشبورد تولید ")
@section("content")

    <div class="row" style="overflow: auto">

        @if($current_allocation)
            <div class=" current col-md-6" style="margin: auto">
                @include("production.machine.short_link._card_info",["machine_allocation"=>$current_machine_allocation,"caption"=>"تخصیص جاری ماشین"])
            </div>
            <div class="w-100"></div>
        @endif
    </div>

    <div class="row" style="text-align: center;padding-right: 35px">
    @include("goods_kind_process.". $machine_allocation_info["route"]."_action")
    </div>
    <div class="row" style="overflow: auto">
        @if( count($reserve_allocation_list) > 0)
            @foreach($reserve_allocation_list as $machine_allocation)
                <div class=" reserve col-md-6" style="margin: auto">
                    @include("production.machine.short_link._card_info",["machine_allocation"=>$machine_allocation,"caption"=>"تخصیص رزرو ماشین"])
                </div>
                <div class="w-100"></div>
            @endforeach

        @endif
    </div>





@endsection
@section("styles")

    <style>
        .btn {
            width: 300px;

        }

        .pcoded-wrapper {
            background: #fff !important;
        }

        .reserve table {
            width: 300px;
            margin-right:  20px;
        }

        .reserve table td {
            border: 3px solid #000;
            color: #000;
            font-weight: bold;
            text-align: center;
        }

        .current table {
            width: 300px;
            margin: 20px;
        }

        .current table td {
            border: 3px solid blue;
            color: blue;
            font-weight: bold;
            text-align: center;
        }
        .current .print {

            color: blue;
        }
        .print{
            color: #000;
        }
    </style>

@endsection
@section("scripts")
    @include("component.script_function.view_image")
@endsection

