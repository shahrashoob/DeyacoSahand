@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
<div class="row">

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5> تزریق مواد اولیه {{$machine->fullCaption()}}
                </h5>
            </div>
            <div class="card-block">
                <form id="form1"
                      action="{{route("fabric.finishing_machine.machine.injection_of_material.submit",$machine)}}"
                method="post"
                autocomplete="off"
                novalidate="novalidate">
                @csrf

                <div class="w-100"></div>

                @if($machine->check_inventory_for_allocation)
                    @include("goods_kind_process.general.machine.injection_of_material._injection")
                @else
                <div class="alert alert-warning">
                    تنظیمات "موجودی مواد اولیه برای انبارک چک شود؟" برای ماشین نادرست است، لطفا با پشتیبانی تماس بگیرید.
                </div>
                @endif

                <br/>

                <div class="col-md-12">

                    <a href="{{route("fabric.finishing_machine.machine.dashboard.view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>
                    <button type="submit" class="btn btn-success">تایید</button>
                </div>


                </form>
            </div>
        </div>

    </div>
    <div class="col-md-12 center">


    </div>
</div>

@endsection
@section("styles")
<script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
<link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "var": "required",
            }
        });
    </script>
@endsection