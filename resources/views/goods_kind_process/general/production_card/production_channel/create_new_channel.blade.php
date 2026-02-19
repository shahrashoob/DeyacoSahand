@extends('layouts.admin._master') @section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5>تعریف کانال تولید جدید برای
                        {{$machine->caption}}
                    </h5></div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."store_new_channel",[$machine,$production])}}"
                          method="post" autocomplete="off" novalidate="novalidate"> @csrf
                        <div class="row">

                            <div class="col-md-12 alert alert-warning">
                                با توجه به اینکه:
                                <b>{!! $message !!}</b>
                                می بایست یک کانال تولید جدید برای ماشین ایجاد شود.
                            </div>

                            @include("component.input._lable",[
                                          "id"=>"channel_type",
                                          "label"=>"نوع کانال تولید",
                                          "value"=>$production_channel_type->caption,
                                            "message"=>""
                                          ])
                            @include("component.input._number",[
                                          "id"=>"capacity",
                                          "label"=>"ظزفیت کانال"." (از".$production_channel_type->min_capacity." تا حداکثر ".$production_channel_type->max_capacity." واحد کالا)"
                                          ])

                            @include("component.input._hidden",[
                                          "id"=>"production_channel_type_id",
                                          "value"=>$production_channel_type->id
                                          ])


                            <div class="col-md-12 ">

                                <a href="{{route($dashboard_route."index",$production)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"
                                        onclick="confirm('آیا از تعریف کانال جدید اطمینان دارید؟')">
                                    افزودن کانال تولید
                                </button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection @section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/> @endsection @section("scripts")
    <script> $('#form1').validate({
            rules: {
                "capacity": {
                    'required': true,
                    "min": {{$production_channel_type->min_capacity}},
                    "max": {{$production_channel_type->max_capacity}}
                }
            }
        }); </script>

@endsection
