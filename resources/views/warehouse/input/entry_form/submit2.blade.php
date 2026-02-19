@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار  ")

@section('content')


    <form id="form1" autocomplete="off" action="{{route("wh.input.entry_form.confirm2")}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>مشخصات کالا</h5>
                    </div>
                    <div class="card-block">


                        <div class="row">

                            @include("component.input._lable",[
                                "label"=>"نام کالا",
                                "value"=>$product?$product->fullCaption():""
                                ])
                            @include("component.input._hidden",[
                                "id"=>"product_id",
                                "value"=>$product->id??""
                                ])




                            @include("component.input._lable",[
                                "label"=>"درجه کالا",
                                "value"=>$degree->caption??""
                                ])
                            @include("component.input._hidden",[
                                "id"=>"degree_id",
                                "value"=>$degree->id??""
                                ])


                            @include("component.input._lable",[
                                "label"=>"لات",
                                "value"=>$lot_number_code
                                ])
                            @include("component.input._hidden",[
                                "id"=>"lot_number_code",
                                "value"=>$lot_number_code
                                ])
                            @if(!isset($lot_number))
                                <div class="col-md-12 alert alert-warning">
                                    شماره همبافت {{$lot_number_code}} در سیستم یافت نشد، در صورت تایید یک همبافت جدید
                                    برای کالا ایجاد می شود.
                                </div>
                            @endif

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>مشخصات انبار</h5>
                    </div>
                    <div class="card-block">


                        <div class="row">

                            @include("component.input._lable",[
                                "label"=>"نام انبار",
                                "value"=>$warehouse->caption??""
                                ])
                            @include("component.input._hidden",[
                                "id"=>"warehouse_id",
                                "value"=>$warehouse->id??""
                                ])


                            @include("component.input._lable",[
                                "label"=>"نوع تراکنش",
                                "value"=>$trans_kind->caption??""
                                ])
                            @include("component.input._hidden",[
                                "id"=>"trans_kind_id",
                                "value"=>$trans_kind->id??""
                                ])

                            @include("component.input._lable",[
                                "label"=>"طرف حساب",
                                "value"=>$opp_kind->caption??""
                                ])
                            @include("component.input._hidden",[
                                "id"=>"opp_kind_id",
                                "value"=>$opp_kind->id??""
                                ])

                            @include("component.input._lable",[
                                "label"=>"مرکز هزینه",
                                "value"=>$cost_center->caption??""
                                ])
                            @include("component.input._hidden",[
                                "id"=>"cost_center_id",
                                "value"=>$cost_center->id??""
                                ])


                            @include("component.input._lable",[
                                "label"=>"شرح تراکنش انبار",
                                "value"=>$description
                                ])
                            @include("component.input._hidden",[
                                "id"=>"description",
                                "value"=>$description
                                ])


                            @include("component.input._hidden",[
                                "id"=>"amount",
                                "value"=>$amount
                                ])


                            @include("component.input._lable",[
                                "label"=>"مقدار کالا",
                                "value"=>$amount
                                ])
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>مشخصات بسته بندی ها</h5>
                    </div>

                    @include("component.input._hidden",["id"=>"packing_form_rows","value"=>($packing_form_rows)])


                </div>
            </div>
            <div class="col-md-12 center">


                <a class="btn btn-outline-dark" href="{{route("wh.input.entry_form.index")}}">بازگشت</a>
                <a class="btn btn-primary" href="{{route("wh.input.entry_form.index",1)}}">ویرایش</a>
                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" style="width: 140px"
                        aria-expanded="false">تایید نهایی
                </button>
                <div class="dropdown-menu" style="text-align: center">
                 

                    <a class="dropdown-item" id="btn_confirm_back">تایید نهایی </a>


                </div>
                @include("component.input._hidden",["id"=>"print","value"=>0])
            </div>

        </div>
    </form>



@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


@section("scripts")
    <script>
        $("#btn_confirm_print").click(function () {
            $("#print").val("print_1");
            $("#form1").submit();
        })
        $("#btn_confirm_back").click(function () {
            $("#print").val("back");
            $("#form1").submit();
        })
        $('#form1').validate({
            rules: {
                "trans_kind_id_auto": "required",
            }
        });
    </script>

@endsection

