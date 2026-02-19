@extends('layouts.admin._master')

@section('page_header_title'," مجوزها ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> جزئیات مجوز  {{$special_license->code}} </h5>
                </div>
                <div class="card-block">


                    <div class="row">

                        @include("utility.special_license.type_view.type".$special_license->special_license_type->id."._confirmation")
                        @include("component.input._lable",["label"=>"وضعیت","value"=>$special_license->status->caption])
                    </div>

                    <a href="{{route("utility.special_license.admin.dashboard.index")}}"
                       class="btn btn-outline-dark">بازگشت </a>




                </div>
            </div>
        </div>
        @include("utility.special_license.panel.dashboard._confirm_expert")
        @include("utility.special_license.panel.dashboard._log")
    </div>

@endsection

@section("styles")

@endsection


