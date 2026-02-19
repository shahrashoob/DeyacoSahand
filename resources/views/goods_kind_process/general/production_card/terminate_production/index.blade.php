@extends('layouts.admin._master') @section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5>خاتمه یافته کردن کارت تولید
                        {{$production->serial}}
                    </h5></div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."submit",$production)}}"
                          method="post" autocomplete="off" novalidate="novalidate"> @csrf
                        <div class="row">
                            <div class="w-25"></div>

                            <div class="col-md-6">
                                @include("component.input._textarea",[
                                              "id"=>"description",
                                              "label"=>"دلیل خاتمه یافته کردن",
                                              ])
                            </div>

                            <div class="col-md-12 center">

                                <a href="{{route($dashboard_route."view_card",$production)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"
                                        onclick="return confirm('آیا از خاتمه یافته کردن اطمینان دارید؟')">
                                    ثبت
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
                "description": "required",
            }
        }); </script>

@endsection
