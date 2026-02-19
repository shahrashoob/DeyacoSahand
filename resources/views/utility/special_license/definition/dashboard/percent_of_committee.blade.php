@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش {{$special_license_type->caption}} </h5>
                </div>
                <div class="card-block">
                    <div class="col-md-12 alert alert-warning">
                        لطفا برای هر کمیته مشخص نمایید که حداقل چند درصد اعضاء کمیته باید مجوز را تایید کنند تا مجوز از
                        نظر کمیته تایید شده تلقی گردد.
                    </div>
                    <form id="form1"
                          action="{{route("utility.special_license.definition.dashboard.submit_percent_of_committee",$special_license_type)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                       @foreach($special_license_type->special_license_type_expert_committee as $item)

                           @include("component.input._number",["id"=>"percent_of_committee_".$item->id,"value"=>$item->min_percent_of_committee, "label"=>$item->committee->caption." در اولویت ".$item->priority_number, $item->min_percent_of_committee])
                       @endforeach
                        </div>

                        <a href="{{route("utility.special_license.definition.dashboard.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    @include("component.input.select2._script")
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "status_id_auto": "required",
            }
        });
    </script>
@endsection
