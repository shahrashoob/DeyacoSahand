@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")

    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن درخواست طراحی (طراحی سریع کالای مشابه) </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("line_product_station.product.product_creation.new_form_quick.submit")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">


                            @include("component.input._text",["id"=>"caption",'label'=>"نام پیشنهادی کالا ","value"=>""])

                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"machine_production_id",
                                    "label"=>"کالای تولید نمونه گیری (جاری ماشین)",
                                    "option"=>$option_list,
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>"",

                                    ])
                            </div>

                            @include("line_product_station.product.copy_from_other._setting_items",["only_hidden"=>1])
                        </div>
                        <div class="w-100"><br/></div>

                        @include("component.input._radio_box01",["id"=>"consumed_break",'label'=>"نیاز به ویرایش اطلاعات کالای مصرفی می باشد؟","value"=>1])
                        @include("component.input._radio_box01",["id"=>"property_break",'label'=>"نیاز به ویرایش مشخصات کالا می باشد؟","value"=>1])
                        @include("component.input._radio_box01",["id"=>"image_break",'label'=>"نیاز به تغییر تصویر کالا می باشد؟","value"=>1])



                        <a href="{{route("line_product_station.product.product_creation.dashboard.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>


                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('آیا از ثبت فرم طراحی کالا اطمینان دارید؟')">ثبت درخواست طراحی سریع
                        </button>



                    </form>

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
    @include("component.script_function.get_new_option")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "machine_production_id": "required",
            }
        });



    </script>
@endsection
