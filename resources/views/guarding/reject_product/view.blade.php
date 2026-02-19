@extends('layouts.admin._master')
@section("page_header_title","داشبورد کنترل کیفیت")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>فرم مرجوعی {{$reject_product_form->code}}</h5>
                </div>
                <div class="card-block">


                    @include("quality_control.reject_product.public._view")
                    <div class="center">
                        <a href="{{route("guarding.reject_product.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        @if($reject_product_form->status_id == 7009004 && $post_user->checkButtonPermission( "guarding.reject_product.confirm_from" ))
                            <a href="{{route("guarding.reject_product.confirm_form",$reject_product_form)}}" onclick="confirm('آیا از تایید فرم مرجوعی اطمینان دارید')"
                               class="btn btn-primary">تایید ورود به کارخانه</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

@endsection
