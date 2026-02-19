@extends('layouts.admin._master')
@section("page_header_title","داشبورد ".$machine_allocation->getTextOfThing("dashboard_caption")."-  ".
$machine_allocation->getTextOfThing("fullCaption")
)

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> خاتمه یافته
                        کردن {{$machine_allocation->getTextOfThing("production_caption")}} {{$machine_allocation->production->serial()}}</h5>
                </div>
                <div class="card-block">


                    <div class="accordion" id="accordionExample">

                        <div class="alert alert-info">
                            <h6>
                                با توجه به اینکه مقدار
                                <b>
                                    {{$result_can_production_terminate["production_amount"]}}
                                    {{$machine_allocation->product->unit->caption??""}}
                                </b>
                                از

                                {{$machine_allocation->getTextOfThing("production_caption")}}


                                تولید (یا ارسال) شده است،
                                و مقدار تخصیص
                                <b>
                                    {{$machine_allocation->allocation_amount}}
                                    {{$machine_allocation->product->unit->caption??""}}
                                </b>
                                بوده است، آیا تولید (یا ارسال) خاتمه یافته است؟


                            </h6>


                        </div>
                        <div class="text text-danger">
                            <b>انتخاب گزینه بله:</b>
                            به این معنی است که تولید (یا ارسال) پایان یافته و دیگر امکان ثبت تولید (یا ارسال) وجود
                            ندارد.
                            <br>
                            <b>انتخاب گزینه خیر:</b>
                            به این معنی است که تولید (یا ارسال) همچنان ادامه دارد.
                        </div>
                        <div class="col-md-12 center">
                            <form id="form1" autocomplete="off"
                                  action="{{route("production.public_module.register_production.submit_terminate_production",[$machine_allocation])}}"
                                  method="post"
                                  novalidate="novalidate">
                                @csrf

                                <a href="{{route("production.public_module.register_production.index",$machine_allocation)}}"
                                   class="btn btn-outline-dark" style="width: 100px">بازگشت</a>

                                @include("component.input._hidden",["id"=>"answer_result","value"=>""])
                                @include("component.input._hidden",["id"=>"confirm_type","value"=>$confirm_type])

                                <button type="button" class="btn btn-primary"  id="btn_yes"

                                        style="width: 140px">
                                    بله
                                </button>
                                <button type="button" class="btn btn-info" id="btn_no"
                                        style="width: 140px">
                                    خیر
                                </button>

                            </form>
                        </div>

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
    <script>
        $('#form1').validate({
            rules: {
                "packing_type_id_auto": "required",
                "degree_id_auto": "required",
                "amount": {
                    required: true,
                    min: 1
                },
                "carrier_code": "required"
            }
        });
        $("#btn_yes").click(function () {
            if (confirm('آیا از  پایان یافتن تولید اطمینان دارید؟')) {
                $("#answer_result").val("yes");
                $("#form1").submit();
                return true;
            }
            return false;
        })
        $("#btn_no").click(function () {
            $("#answer_result").val("no");
            $("#form1").submit();
            return true;
        })
    </script>
@endsection
