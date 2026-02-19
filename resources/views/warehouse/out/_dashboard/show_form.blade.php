@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  خروج از انبار  ")

@section('content')
    <div class="row">

        @include("warehouse.out.exit_form.qr._index")
        <div class="col-md-12" style="text-align: center">
            <a href="{{route("wh.out.dashboard.view",[$product_request_form,$page])}}"
               class="btn btn-outline-dark">بازگشت</a>

            <div class="btn-group mb-2 mr-2">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">دانلود کارت خروج از انبار
                </button>
                <div class="dropdown-menu" x-placement="bottom-start"
                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 43px, 0px);">
                    <a class="dropdown-item"
                       href="{{route("wh.out.exit_form.download",[$product_request_form,$form,4])}}">A4</a>
                    <a class="dropdown-item"
                       href="{{route("wh.out.exit_form.download",[$product_request_form,$form,3])}}">A5</a>
                    <a class="dropdown-item"
                       href="{{route("wh.out.exit_form.download",[$product_request_form,$form,1])}}">95*123</a>
                </div>
            </div>

        </div>

    </div>




@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection




