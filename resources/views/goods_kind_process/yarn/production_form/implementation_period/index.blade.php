@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  ریسندگی  ")

@section('content')


    <form id="form1" autocomplete="off" action="{{route("yarn.production_form.implementation_period_form.submit")}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>ثبت فرم تولید نخ ( ویژه دوره پیاده سازی) </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">


                            <div class="col-md-12">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"product_id",
                                    "label"=>"انتخاب نخ ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>


                        </div>

                    </div>
                    <div class="col-md-12">
                        <a href="{{route("yarn.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>

                    </div>
                </div>

            </div>
        </div>

    </form>


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
                "product_id_auto": "required"
            }
        });
    </script>
@endsection


