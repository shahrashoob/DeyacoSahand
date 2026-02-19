@extends('layouts.admin._master')

@section('page_header_title'," ")

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>  جایگزین کردن کارت تولید {{$production->serial()}}</h5>
            </div>
            <div class="card-block">
                <form id="form1" autocomplete="off" action="{{route("production.submit_confirm_replace",$production)}}" method="post" novalidate="novalidate">
                    @csrf

                    @include("production.production_card._info_small")
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                @include("component.input._hidden",["id"=>"replace_id","value"=>$replace->id])

                                @include("component.input._lable",["id"=>"replace_serial","label"=>"سریال کارت تولید جایگزین  ","value"=>$replace->serial()])

                                @include("component.input._lable",["id"=>"replace_serial","label"=>" تعداد تولید کارت جایگزین  ","value"=>$replace->number_product()])

                            </div>
                        </div>

                    </div>

                    <a href="{{route("production.replace",$production)}}" class="btn btn-outline-defualt">بازگشت</a>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('آیا از  جایگزین کردن کارت تولید اطمینان دارید')"> تایید نهایی جایگزین کردن </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
@section("styles")

@include("component.input.datepicker._script")
@endsection

@section("scripts")
<script>


        $('#form1').validate({
            rules: {
                pre_factor_type_id_auto: "required",
                condition_type_id_auto: "required",
                caption: "required"
            }
        });

    </script>
@endsection

