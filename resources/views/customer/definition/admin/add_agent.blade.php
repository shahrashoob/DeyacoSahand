@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت مشتریان")
@section("content")
    <form id="form1" style="display: inline"
          action="{{route("customer_group.definition.admin.submit_agent",$customer)}}" method="post"
          novalidate="novalidate" autocomplete="off">
        @csrf


        <div class="row">

            <div class="col-md-6">

                <div class="card">
                    <div class="card-header" >
                        <h5>افزودن نماینده برای مشتری
                            {{$customer->code ." - ".$customer->caption}}</h5>
                    </div>


                    <div class="card-block" style="min-height: 750px">

                        @include("customer.definition.admin._agent_info")
                    </div>
                </div>
            </div>
            <div class="col-md-6">

                <div class="card">
                    <div class="card-header" >
                        <h5>اطلاعات آدرس و تماس</h5>
                    </div>
                    <div class="card-block overflow-auto" style="min-height: 750px">


                        <div class="row" >
                            @include("customer.definition.admin._address_item_input")
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-12" style="text-align: center">
                <a href="{{route("customer_group.definition.admin.edit",$customer)}}"
                   class="btn btn-outline-dark btn-lg">بازگشت</a>

                <button type="submit" class="btn btn-primary btn-lg"> ذخیره</button>
            </div>
        </div>

    </form>
@endsection
@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>

        $('#form1').validate({
            rules: {

                firstname: "required",
                lastname: "required",
                gender_id: "required",
                agent_type_id: "required",
                date_of_birth_value: "required",
                province_id_auto: "required",
                country_id_auto: "required",
                national_code: {
                    required: true,
                    number: true,
                    minlength: 10, maxlength: 10
                },
                mobile: {required: true, minlength: 10, maxlength: 10},
                phone: {required: true, minlength: 11, maxlength: 11},
                address: "required",
                country: "required"
            }
        });
    </script>
@endsection
