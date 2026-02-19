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
                        <a href="{{route("quality_control.dashboard.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        @if($reject_product_form->status_id == 7009003 && $post_user->checkButtonPermission( "quality_control.reject_product.cheek_quality.index" ))
                            <a href="{{route("quality_control.reject_product.confirm_quality.confirm_reject_product_form",$reject_product_form)}}"
                               class="btn btn-primary" onclick="return confirm('آیا از تایید فرم اطمینان دارید؟')">تایید کنترل کیفیت</a>
                        @elseif($reject_product_form->status_id == 7009008 && $post_user->checkButtonPermission( "quality_control.reject_product.confirm_sale_expert.confirm_reject_product_form" ))
                            <a href="{{route("quality_control.reject_product.confirm_sale_expert.confirm_reject_product_form",$reject_product_form)}}"
                               class="btn btn-primary" onclick="return confirm('آیا از تایید فرم اطمینان دارید؟')">تایید کارشناس فروش</a>
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
