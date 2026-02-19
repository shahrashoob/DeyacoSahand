<form id="form_api">
    <div class="card">
        <div class="card-header">
            <h5> ثبت دریافت کالا با کد(پین) بسته بندی / شماره پالت </h5>

            <div class="row">
                <div class="col-md-3"></div>
                <div class=" col-md-6 col-sm-12 center">
                    <div class="row">
                        @if(isset($error_message))

                            <div class="alert alert-danger col-md-12">
                                {!! $error_message !!}
                            </div>
                            <script>
                                var snd = new Audio("{{asset("assets/voice/alarm.mp3")}}");
                                snd.play();
                            </script>

                        @endif
                        @if(isset($warning_message))

                            <div class="alert alert-warning col-md-12 div_warning">
                                {!! $warning_message !!}
                            </div>
                            <script>
                                var snd = new Audio("{{asset("assets/voice/warning.mp3")}}");
                                snd.play();
                            </script>
                            @if(!isset($pallet_code))
{{--                                تایید تغییر کالا--}}
                                <div class="col-md-12 col-sm-12 div_warning" style="margin-top: 10px">
                                    <button type="button" id="btn_warning_ok" class="btn btn-primary btn-sm"
                                            style="width: 50%; display: inline"
                                            >تایید و ادامه
                                    </button>
                                    <button type="button" id="btn_warning_cancel" class="btn btn-danger btn-sm"
                                            style="width: 50%"
                                            >عدم تایید
                                    </button>
                                </div>
                                    @include("component.input._hidden",["id"=>"before_packing_code","value"=>$packing_code])
                            @else
{{--                                تایید تعداد بسته بندی داخل پالت--}}
                                    <div class="col-md-12 col-sm-12 div_warning" style="margin-top: 10px">
                                        <input id="pallet_packing_count" type="number" style="width: 140px"
                                               value="" >
                                        <br/>
                                        <br/>

                                        <button type="submit" id="btn_pallet_count_ok" class="btn btn-primary btn-sm"
                                                style="width:140px; display: inline"
                                                >تایید و ادامه
                                        </button>
                                        <button type="button" id="btn_warning_cancel" class="btn btn-danger btn-sm"
                                                style="width: 140px"
                                                >بازگشت
                                        </button>
                                    </div>
                                    @include("component.input._hidden",["id"=>"packing_code","value"=>$packing_code])
                            @endif

                        @endif
                        @if(isset($success_message))

                            <div class="alert alert-success col-md-12">
                                {!! $success_message !!}
                            </div>
                            <script>
                                var snd = new Audio("{{asset("assets/voice/beep.mp3")}}");
                                snd.play();
                            </script>
                        @endif
                    </div>
                    <div id="div_input" class="row" style="display: {{isset($warning_message)?"none":""}}">

                        <div class="col-md-3 col-sm-12" style="font-size: 16px;font-weight: bold;margin-top: 10px">
                            @if(isset($number_packing_submit))
                                <span> {{$number_packing_submit}}</span>
                                بسته ثبت گردید
                            @endif
                        </div>
                        <div class="col-md-5 col-sm-12" style="margin-top: 10px">
                            کد
                            <input id="packing_code" type="text" style="width: 140px"
                                   value="">
                        </div>


                        <div class="col-md-4 col-sm-12" style="margin-top: 10px">
                            <button type="submit" class="btn btn-primary btn-sm" style="width: 50%"
                                    id="submit_packing_code">بررسی و ثبت
                            </button>

                        </div>

                        {{--<div class="col-md-12">--}}
                        {{--    <a href="{{route("wh.dashboard.set_to_warehouse_all")}}" class=""  >ثبت و بررسی به صورت جمعی</a>--}}
                        {{--</div>--}}
                    </div>


                </div>

                @include("component.input._hidden",["id"=>"last_product_id_read","value"=>$last_product_id_read??""])

                <div class="col-md-6 center" style="margin:auto">

                    @if(isset($packing_message) && $number_packing_submit>0)

                        <div class="alert alert-info ">
                            لیست بسته بندی های ثبت شده:
                            <br/>
                            @foreach(explode(',',$packing_message) as $row)
                                {{$row}} <br/>
                            @endforeach

                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $("#packing_code").focus();
    $("#pallet_packing_count").focus();

    var lock_submit = false;
    $("#form_api").submit(function () {

        add_packing();

        return false;
    });

    function add_packing() {
        if (lock_submit == true) {
            alert("سامانه در ثبت دریافت کالا می باشد، لطفا چند ثانیه صبر کنید.");
            return false;
        }

        lock_submit = true;

        var carrier_code = $("#carrier_code").val();
        var packing_code = $("#packing_code").val();
        var last_product_id_read = $("#last_product_id_read").val();
        var pallet_packing_count = $("#pallet_packing_count").val();

        request = $.ajax({
            url: "{{url("api/warehouse/input/submit_new_packing_api")}}",
            type: "post",
            data: {
                "carrier_code": carrier_code,
                "packing_code": packing_code,
                "pallet_code": '{{$pallet_code??""}}',
                "pallet_packing_count": pallet_packing_count,
                "user_id": {{$user_id}},
                "number_packing_submit": {{$number_packing_submit}},
                "packing_message": '{{$packing_message??""}}',
                "checking_product_change_in_entry": '{{$checking_product_change_in_entry??0}}',
                "last_product_id_read": last_product_id_read
            }
        });
        request.done(function (response, textStatus, jqXHR) {
            lock_submit = false;
            $("#confirm_packing").html(response);
        });
        request.fail(function (jqXHR, textStatus, errorThrown) {
            lock_submit = false;
            // Log the error to the console
            alert("اطلاعات ثبت نشد، لطفا دوباره تلاش کنید.")
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });

    }

    /* warning**/
    $("#btn_warning_ok").click(function () {
        $("#packing_code").val($("#before_packing_code").val());
        $("#last_product_id_read").val("");
        $("#form_api").submit();
    });
    $("#btn_warning_cancel").click(function () {
        $(".div_warning").css("display", "none")
        $("#div_input").css("display", "")
        $("#packing_code").focus();
    });



</script>

