@extends('layouts.admin._master')

@section("page_header_title"," داشبورد طراحی - بافندگی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم طراحی {{$design_form->getCode()}}</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("fabric_raw.design_form.start_designing.submit",$design_form)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        @include("goods_kind_process.fabric_raw.design_form._info_small")

                        <div class="row">

                            @include("component.input._radio",["id"=>"design_available","lable"=>" آیا طراحی در انبار موجود هست؟","radios"=>$radio_option])



                            @include("component.input._radio",["id"=>"it_has_pinning","lable"=>" آیا طراحی لامل ریزی دارد؟","radios"=>$radio_option])



                            @include("component.input._radio",["id"=>"need_to_convert","lable"=>" آیا طراحی نیاز به تبدیل دارد؟","radios"=>$radio_option])

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">شروع طراحی</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        $("#need_to_convert_radio_section").hide();
        $("#it_has_pinning_radio_section").hide();

        $("input[name='design_available']").click(function () {
            if (this.value == 1) {
                $("#need_to_convert_radio_section").show();
                $("#it_has_pinning_radio_section").show();
            } else {
                $("#need_to_convert_radio_section").hide();
                $("#it_has_pinning_radio_section").hide();
            }


        });
        $('#form1').validate({
            rules: {
                "design_available": "required",
                "need_to_convert": "required",
                "it_has_pinning": "required",
            }
        });
    </script>
@endsection
