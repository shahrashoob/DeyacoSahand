@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار  ")

@section('content')

    @if($search_exist_form_model)
        {{--                        فرم های خروجی که به صورت دستی یا با دستیار دیجیتال خروجی کشیدن--}}
        @include("warehouse.out.dashboard._exit_form_no_request")
    @endif
    @if(count($packing_form_read_ids)>0)
        <div class="row">
            <div class="col-md-12" id="card-block">

                <div class="card">
                    <div class="card-header">
                        <h5> تکمیل فرم خروج از انبار </h5>
                    </div>
                    <div class="card-block" id="card-block">
                        <h6>مشخصات انبار</h6>
                        <hr>
                        <div class="col-md-12 alert alert-warning">
                           <h5>
                               با توجه به اینکه شما قبلا
                               {{count($packing_form_read_ids)}}
                               بسته بندی را جهت خروج ثبت نموده اید، لطفا ابتدا وضعیت آنها را مشخص نمایید.
                           </h5>
                            <br/>
                            <a href="{{route("wh.out.exit_form_implementation2.read_packing_forms")}}" class="btn btn-primary">تکمیل فرم خروج قبلی</a>
                            <form id="form1" autocomplete="off" action="{{route("wh.out.exit_form_implementation2.remove_packing_data",count($packing_form_read_ids))}}"
                                  method="post"
                                  novalidate="novalidate">
                                @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('آیا از حذف اطلاعات اطمینان دارید؟')">حذف بسته بندی های و ایجاد فرم جدید</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    @else
        <form id="form1" autocomplete="off" action="{{route("wh.out.exit_form_implementation2.submit")}}"
              method="post"
              novalidate="novalidate">
            @csrf
            <div class="row">
                <div class="col-md-12" id="card-block">

                    <div class="card">
                        <div class="card-header">
                            <h5> فرم خروج از انبار </h5>
                        </div>
                        <div class="card-block" id="card-block">
                            <h6>مشخصات انبار</h6>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    @include("component.input._select",[
                                    "id"=>"trans_kind_id",
                                    "label"=>" نوع تراکنش  ",
                                    "option"=>$trans_kind_option["items"],
                                    "val"=>$trans_kind_option["value"],
                                    "text"=>$trans_kind_option["text"],
                                    "class_col"=>""
                                    ])
                                </div>
                                <div class="col-md-3">
                                    @include("component.input._aotocomplet2",[
                                    "id"=>"cost_center_id",
                                    "label"=>" مرکز هزینه ",
                                    "option"=>$cost_center_option["items"],
                                    "val"=>$cost_center_option["value"],
                                    "text"=>$cost_center_option["text"],
                                    "class_col"=>""
                                    ])
                                </div>

                                <div class="col-md-3">
                                    @include("component.input._aotocomplet2",[
                                    "id"=>"warehouse_id",
                                    "label"=>"انبار  ",
                                    "option"=>$warehouse_option["items"],
                                    "val"=>$warehouse_option["value"],
                                    "text"=>$warehouse_option["text"],
                                    "class_col"=>""
                                    ])
                                </div>

<div class="col-md-12"></div>

                                <div class="col-md-3">
                                    @include("component.input._select",[
                                    "id"=>"opp_kind_id",
                                    "label"=>" طرف حساب ",
                                    "option"=>$opp_kind_option["items"],
                                    "val"=>$opp_kind_option["value"],
                                    "text"=>$opp_kind_option["text"],
                                    "class_col"=>""
                                    ])
                                </div>

                                <div class="col-md-3">
                                    @include("component.input._select",[
                                    "id"=>"warehouse_storage_type_id",
                                    "label"=>"نوع انبارش کالا ",
                                    "option"=>$warehouse_storage_type_option["items"],
                                    "val"=>$warehouse_storage_type_option["value"],
                                    "text"=>$warehouse_storage_type_option["text"],
                                    "class_col"=>""
                                    ])
                                </div>

                                @include("component.input._textarea",["label"=>"شرح تراکنش","id"=>"description","value"=>$description,"width"=>"", "height"=>"50px"])
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 center">
                        <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>
                    </div>
                </div>

            </div>
        </form>
    @endif

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection

@section("scripts")

    <script>
        $('#form1').validate({
            rules: {
                "trans_kind_id": "required",
                "opp_kind_id": "required",
                "cost_center_id_auto": "required",
                "warehouse_id_auto": "required",
                "description": "required",
                "warehouse_storage_type_id": "required",
            }
        });
    </script>
@endsection
