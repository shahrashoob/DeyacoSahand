@extends('layouts.admin._master') @section('page_header_title',"داشبورد  تولید")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> برگشت مواد اولیه به انبار</h5></div>
                <div class="card-block">


                    <div id="panel_change_grade">
                        <div class="row">

                            @include("goods_kind_process.general.machine.material_return_to_warehouse._change_grade_list")

                        </div>

                        <div class="row">

                            @include("goods_kind_process.general.machine.material_return_to_warehouse._waste_list")

                        </div>

                        <div class="row">

                            @include("goods_kind_process.general.machine.material_return_to_warehouse._packing_form_list",["caption_list"=>" لیست بسته بندی های در حال خروج از انبارک"])

                        </div>
                    </div>
                    <div class="col-md-12 center">
                        <form id="form1"
                              action="{{route($route_path."submit_confirm",[$machine,$warehouse])}}"
                              method="post" autocomplete="off" novalidate="novalidate">
                            @csrf
                            <a href="{{route($dashboard_route."view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('آیا تایید فرم اطمیان دارید؟')">
                                تایید و ثبت نهایی

                            </button>
                            <script>
                                $("#change_of_grade_yes,#change_of_grade_no").change(function () {
                                    if ($(this).val() == 1) {
                                        $("#panel_change_grade").css("display", "");
                                    } else {
                                        $("#panel_change_grade").css("display", "none");
                                    }

                                    $("#change_of_grade").val($(this).val())
                                })
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/> @endsection @section("scripts")
    <script> $('#form1').validate({
            rules: {
                "description": "required",
            }
        });
    </script>

@endsection
@section("scripts")


@endsection

