@extends('layouts.admin._master') @section('page_header_title',"داشبورد  تولید")
@section('content')

    <div class="card-block">
        <form id="form1"
              action="{{route($route_path."submit",$machine)}}"
              method="post" autocomplete="off" novalidate="novalidate"> @csrf

            @include("goods_kind_process.general.machine.injection_of_material._injection")

            @if($allow_get_contour)
                <div class="row">

                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h5> کنتور ماشین
                                </h5>
                            </div>
                            <div class="card-block">
                                @include("goods_kind_process.fabric_raw.public._shift_and_counter")
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-12 center">

                <a href="{{route($dashboard_route."view",$machine)}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-success"
                        onclick="return confirm('آیا از تزریق مواد اولیه اطمینان دارید؟')">
                    تایید فرم
                </button>
            </div>
        </form>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script> $('#form1').validate({
            rules: {
                "description": "required",
            }
        }); </script>
@endsection
