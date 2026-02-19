@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>پرینت کارت تخصیص {{$machine->fullCaption()}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="" style="border: 2px solid #000; width: 400px; margin: auto">
                        @include("goods_kind_process.warps.matthys.machine.allocation_card._band_info")
                    </div>
                </div>
                <div class="col-md-12 center">
                    <a href="{{route("warps.matthys.machine.dashboard.view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>
                    <a href="{{route("warps.matthys.machine.allocation_card.print",[$machine,$allocation])}}" class="btn btn-primary"><i class="fa fa-print"></i> چاپ </a>
                    <a href="{{route("warps.matthys.machine.allocation_card.download",[$machine,$allocation])}}" class="btn btn-primary"><i class="fa fa-download"></i> دانلود </a>

                </div>
            </div>

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        td,table{
            border: 1px solid #000;
        }
        table{
            width: 100%;
        }

    </style>
@endsection
