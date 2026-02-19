
@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  ریسندگی  ")

@section('content')


    <form id="form1" autocomplete="off"
          action="{{route("yarn.production_form.implementation_period_form.submit_form",$form)}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>تایید نهایی فرم تولید نخ ( ویژه دوره پیاده سازی) </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">
                            <div class="col-md-12">
                                <h4>{{$product->caption}}</h4>
                            </div>


                        </div>
                     @include("goods_kind_process.yarn.public._form_details")
                        <div style="text-align: center">
                            <a href="{{route("yarn.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-success">تایید فرم </button>

                        </div>
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
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",
            }
        });
    </script>
@endsection


