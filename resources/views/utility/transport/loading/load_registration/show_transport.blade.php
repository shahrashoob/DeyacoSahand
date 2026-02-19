@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])
@section("page_header_title","داشبورد ارسال بار ")

@section('content')



    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> بار شماره: {{$transport->getCode()}} </h5>
                </div>

                <div class="card-block">

                    @include("utility.transport.public._transport_info")

                </div>
            </div>
        </div>

        @if($transport->status_id ==6010103 )
            @if(count($packing_form_data) != array_sum($packing_form_data))
                <div class="col-sm-12" id="panel_packing">
                    <div class="card">
                        <div class="card-header">
                            <h5> اطلاعات بسته بندی های موجود در بار </h5>
                        </div>

                        <div class="card-block">
                            <div class="row">
                                <div class="col-md-12 center">
                                    <form id="form_api">
                                        <h5>تعداد کل بسته بندی ها <span
                                                class="badge badge-secondary">{{count($packing_form_data)}}</span></h5>
                                        <h5>تعداد بسته بندی کنترل شده <span class="badge badge-secondary"
                                                                            id="sum_of_confirm_packing_form">{{array_sum($packing_form_data)}}</span>
                                        </h5>
                                        <h5>آخرین بسته بندی ثبت شده <span class="badge badge-secondary"
                                                                          id="last_packing_form">---</span>
                                        </h5>
                                        <h5> <span class="badge text-danger"
                                                                          id="packing_not_register"></span>
                                        </h5>
                                        <div class="col-md-12 col-sm-12 " style="margin-top: 10px">
                                            <input type="radio" id="type_of_reading_1" name="type_of_reading" value="packing_form_code" checked><label for="type_of_reading_1"> بر اساس کد بسته بندی </label>
                                            <input type="radio" id="type_of_reading_2" name="type_of_reading" value="transport_code"> <label for="type_of_reading_2">  بر اساس کد بسته بندی حمل و نقل </label>
                                        </div>
                                        <h5 class="text-danger">
                                            <span id="error_message"></span>
                                        </h5>

                                        <input id="packing_code" autofocus type="text"
                                               style="width: 140px;height: 33px;margin-bottom: 15px"
                                               value="">
                                        <br/>
                                        <button type="submit" class="btn btn-primary btn-sm" id="submit_packing_code"
                                                style="width: 140px">بررسی
                                            و
                                            ثبت
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            @endif


            <div class="col-md-12 center">

                <form id="form1" autocomplete="off"
                      action="{{route("utility.transport.loading.load_registration.confirm_transport",[$transport])}}"
                      method="post"
                      novalidate="novalidate">
                    @csrf

                    {{--                در حال بارگیری--}}
                    <input type="hidden" name="packing_form_data_last" id="packing_form_data_last" value="">
                    <input type="hidden" name="confirm_type" value="confirm">
                    @if(count($packing_form_data) != array_sum($packing_form_data))
                        <div  id="btn_submit" style="display: none">
                            <button type="submit" class="btn btn-primary" >
                                 تایید بارگیری
                            </button>
                            <a href="{{route("utility.transport.loading.load_registration.show_packing_list",$transport)}}" type="submit" class="btn btn-primary"  >
                                لیست بسته بندی های خوانده شده
                            </a>
                        </div>
                    @else
                        <button type="submit" class="btn btn-primary">
                            تایید بارگیری
                        </button>
                    @endif


                </form>
            </div>

{{--            <div class="col-sm-12" id="panel_packing">--}}
{{--                <div class="card">--}}
{{--                    <div class="card-header">--}}
{{--                        <h5>      لیست بسته بندی های خوانده شده </h5>--}}
{{--                    </div>--}}

{{--                    <div class="card-block">--}}
{{--                        <br/>--}}
{{--                        @php $k=0; @endphp--}}
{{--                        @foreach($packing_form_list as $item)--}}
{{--                            {{$item->code}} ,--}}
{{--                            @if($k%10 == 0)--}}
{{--                                <br/>--}}
{{--                            @endif--}}
{{--                            @php $k++; @endphp--}}
{{--                        @endforeach--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        @else
          <div class="col-md-12">
              <a href="{{route("utility.transport.loading.dashboard.index")}}"
                 class="btn btn-outline-dark">بازگشت</a>

              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false">دانلود/ چاپ بارنامه
              </button>
              <div class="dropdown-menu" style="text-align: center">

                  <a href="{{route("utility.transport.loading.load_registration.print_transport_card",[$transport,$transport->random,"A4"])}}" class="dropdown-item" id="btn_confirm_print4"> چاپ بارنامه (A4)</a>
                  <a href="{{route("utility.transport.loading.load_registration.print_transport_card",[$transport,$transport->random,"A5"])}}" class="dropdown-item" id="btn_confirm_print3"> چاپ بارنامه (A5)</a>

                  <a href="{{route("utility.transport.loading.load_registration.download_transport_card",[$transport,$transport->random,"A4"])}}" class="dropdown-item" id="btn_confirm_print4"> دانلود بارنامه (A4)</a>
                  <a href="{{route("utility.transport.loading.load_registration.download_transport_card",[$transport,$transport->random,"A5"])}}" class="dropdown-item" id="btn_confirm_print3"> دانلود بارنامه (A5)</a>



              </div>



          </div>
        @endif

    </div>

@endsection
@section("styles")

    @include("component.input.datepicker._script")
    <style>
        .form-group {
            margin: 0px !important;
        }

        .form-control {
            width: 150px !important;
            margin: auto;
        }
    </style>
@endsection
@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                "caption": "required",
            }
        });
        packing_form_data = {!! json_encode($packing_form_data) !!};
        packing_form_data_server = packing_form_data;
        transport_packing_form_ids = {!! json_encode($transport_packing_form_ids) !!};
        sum_accepted = {{array_sum($packing_form_data)}};
        $("#form_api").submit(function () {

            var packing_code = $("#packing_code").val();
            $("#packing_code").val("");
            $("#error_message").html("")
            packing_code = parseInt(packing_code) - 1000;

            if ($("#type_of_reading_1").is(":checked") == 1) {
                add_packing(parseInt(packing_code));
            } else {
                add_transport_item(packing_code);
            }

            $("#packing_code").focus();
            return false;
        });
        var alarm = new Audio("{{asset("assets/voice/alarm.mp3")}}");
        var beep = new Audio("{{asset("assets/voice/beep.mp3")}}");

        function add_packing(packing_form_id , allow_alarm=true) {

            if (packing_form_id in packing_form_data) {


                if (parseInt(packing_form_data[packing_form_id]) == 0) {
                    if (allow_alarm){
                        beep.play();
                    }
                    sum_accepted++;
                } else {
                    $("#error_message").html(" کد بسته "+("DCPK/"+(packing_form_id+1000))+"بندی قبلا خوانده شده است.")
                    alarm.play();
                }

                packing_form_data[packing_form_id] = 1;
                $("#sum_of_confirm_packing_form").html(sum_accepted);
                $("#last_packing_form").html("DCPK/" + (packing_form_id + 1000));
            } else {
                $("#error_message").html("کد بسته بندی نامعتبر است.")
                alarm.play();
            }
            $("#btn_submit").css("display", "none");
            if (sum_accepted == {{count($packing_form_data)}}) {
                send_to_server();
                setTimeout(function () {
                    $("#btn_submit").css("display", "")
                    $("#panel_packing").css("display", "none")
                }, 5000)

            }
            $("#packing_form_data_last").val(JSON.stringify(packing_form_data));
        }

        function add_transport_item(transport_item_id){
            k = 0;
            // خواندن بسته بندی حمل و نقل
            $.each(transport_packing_form_ids, function( index, value ) {
               if (transport_item_id == value){

                   add_packing(parseInt(index),false);
                   k++;
               }

            });
            if(k>0){
                beep.play();
            }
            else{
                $("#error_message").html("کد بسته بندی حمل و نقل نامعتبر است.")
                alarm.play();
            }

        }

        function send_to_server() {

            request = $.ajax({
                url: "{{url("api/utility/transport/loading/load_registration/control_packing_api")}}",
                type: "post",
                data: {
                    "packing_form_data": JSON.stringify(packing_form_data),
                    "transport_id": {{$transport->id}},
                }
            });
            request.done(function (response, textStatus, jqXHR) {
                $("#btn_submit").css("display", "");
                packing_form_data_server = JSON.parse(response);
                text_error="";
                for (let index in packing_form_data){
                      if(packing_form_data[index] ==1  && packing_form_data_server[index]+0 !=1){
                   text_error+="DCPK/"+(parseInt(index)+1000)+",";
                     }
                }
                if(text_error!=""){
                    $("#btn_submit").css("display", "none");
                    text_error="بسته بندی های ذیل به دلیل قطعی شبکه ثبت نشده است، لطفا یکبار دیگر آنها را بخوانید"+"<br/>"+text_error;
                }
                $("#error_message").html(text_error);

            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                alert("اطلاعات ثبت نشد، لطفا دوباره تلاش کنید.")
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });


        }


        @if(count($packing_form_data) != array_sum($packing_form_data))
        var run = setInterval("send_to_server()", 5000)
        @endif
    </script>
@endsection
