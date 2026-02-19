@extends('layouts.admin._master')

@section('page_header_title'," ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دریافت و ثبت فایل xml سفارش {{$order->code()}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("sales.dashboard.register_xml_submit",$order)}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        @include("component.input._text",["id"=>"register_xml_code","label"=>"کد ثبت xml سفارش کار نوسا","value"=>$order->register_xml_code])
                        <br/>
                        <br/>
                        <div class="text-center">
                            <a href="{{route("sales.dashboard.index")}}" class="btn btn-outline-dark" type="button">
                                <i class="fa fa-arrow-right"></i> بازگشت
                            </a>
                            <button id="btn_confirm" class="btn btn-success " type="submit">
                                </i> ثبت کد
                            </button>
                            <a href="{{route("sales.print.register_xml_download",$order)}}"><i
                                    class="fa fa-download"></i> دانلود فایل xml سفارش</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection

@section("scripts")

@endsection

