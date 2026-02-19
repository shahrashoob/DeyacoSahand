@extends('layouts.admin._master')

@section('page_header_title'," مجوزها ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> جزئیات مجوز {{$special_license->code}} </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("utility.special_license.panel.dashboard.confirm",$special_license)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <h6> آقای/ خانم {{$confirmation_worker->fullname()}}</h6>
                            </div>
                            @include("utility.special_license.type_view.type".$special_license->special_license_type->id."._confirmation")

                            @include("component.input._textarea",["lable"=>"توضیحات","id"=>"description","class_col"=>"col-md-9","height"=>"80px"])
                            @include("component.input._hidden",["id"=>"confirm_type","value"=>""])

                        </div>
                        @if($worker->id == $confirmation_worker->id)

                            <div class="col-md-12 alert alert-info">
                                با توجه به اینکه شما درخواست را ایجاد کرده اید، امکان تایید آن برای شما وجود ندارد، در
                                صورت لزوم پست مافوق شما می تواند درخواست را تایید نماید.
                            </div>
                        @endif

                        <a href="{{route("utility.special_license.panel.dashboard.index")}}"
                           class="btn btn-outline-dark">بازگشت </a>


                        @if($worker->id == $confirmation_worker->id)
                            <button  type="submit" id="send_to_parent"
                               class="btn btn-primary">
                                ارسال درخواست به مافوق
                            </button>

                        @else
                            <button type="submit" id="confirm_btn" class="btn btn-success">
                                تایید درخواست
                            </button>
                            <button type="submit" id="reject_btn" class="btn btn-danger">
                                عدم تایید درخواست
                            </button>
                        @endif


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
        $("#confirm_btn").click(function (){
            $("#confirm_type").val("confirm");
        })
        $("#send_to_parent").click(function (){
            $("#confirm_type").val("send_to_parent");
        })
        $("#reject_btn").click(function (){
            $("#confirm_type").val("reject");
        })
        $('#form1').validate({
            rules: {
                "description1":"required"
            }
        });
    </script>
@endsection

