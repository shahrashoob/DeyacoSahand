@extends('layouts.admin._master',["keypress_enable"=>1])
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست بسته بندی های ارسال بار
                        {{$transport_item->transport->getCode()}}
                        @if($transport_item->transport->order)
                            -
                            سفارش {{$transport_item->transport->order->code()}}
                        @endif
                    </h5>

                </div>
                <div class="card-block" id="card_packing" style="overflow: auto">

                 @include("utility.transport.dashboard._packing_add")

                </div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
