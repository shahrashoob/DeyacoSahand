@extends('layouts.admin._master')
@section("page_header_title","گزارش 1003 - گردش کالا")

@section("content")
    <div class="row">
        @if(count($list_large_operation)>0)
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <h5>دانلود گزارش ها</h5>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th>کد درخواست</th>
                                    <th> زمان ثبت درخواست گزارش</th>
                                    <th>زمان ایجاد گزارش</th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($list_large_operation as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            {{$item->id}}
                                        </td>
                                        <td>
                                            {{$item->get_created_at()}}
                                        </td>
                                        <td>
                                            {{$item->get_updated_at()}}
                                        </td>
                                        <th>

                                                <a href="{{route("report.1003.download_report",$item)}}">
                                                    <i class="fa fa-download"></i>
                                                    دریافت فایل
                                                    گزارش</a>

                                        </th>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>گزارش 1003- گردش کالا</h5>
                </div>

                <div class="card-block">
                    <form id="form1" autocomplete="off" action="{{route("report.1003.submit_form")}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            @include("component.input.datepicker._datepicker",["id"=>"start_date","lable"=>"از تاریخ "])

                            @include("component.input.datepicker._datepicker",["id"=>"end_date","lable"=>" تا تاریخ "])


                            <div class="w-100"></div>
                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"from_product_id",
                                    "label"=>" کالا ( از شناسه) ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"to_product_id",
                                    "label"=>"  کالا (تا شناسه) ",
                                    "option"=>$product_option["items"],
                                    "val"=>$product_option["value"],
                                    "text"=>$product_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"from_warehouse_id",
                                    "label"=>" انبار از ",
                                    "option"=>$warehouse_option["items"],
                                    "val"=>$warehouse_option["value"],
                                    "text"=>$warehouse_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="col-md-3">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"to_warehouse_id",
                                    "label"=>"  انبار تا ",
                                    "option"=>$warehouse_option["items"],
                                    "val"=>$warehouse_option["value"],
                                    "text"=>$warehouse_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"breaking_by_lot_number",'label'=>"گزارش به تفکیک همبافت (لات) "])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"breaking_by_degree",'label'=>"گزارش به تفکیک درجه "])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"breaking_by_packing_item",'label'=>"گزارش به تفکیک اقلام بسته بندی "])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"show_zero_inventory",'label'=>"نمایش کالا ها با مانده صفر ","checked"=>1])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"show_zero_input",'label'=>"تراکنش های ورودی ","checked"=>1])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"show_zero_output",'label'=>"تراکنش های خروجی ","checked"=>1])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"breaking_by_transaction",'label'=>"به تفکیک تراکنش","checked"=>0])
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"breaking_by_classification",'label'=>"به تفکیک طبقه بندی - کالا","checked"=>0])
                            </div>

                            <div class="w-100"><br/></div>
                            <div class="col-md-3 classification_group" >
                                @include("component.input._select",[
                                    "id"=>"goods_kind_id",
                                    "label"=>"رسته کالایی  ",
                                    "option"=>$goods_kind_option["items"],
                                    "val"=>$goods_kind_option["value"],
                                    "text"=>$goods_kind_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"><br/></div>
                            <div class="col-md-3 classification_group" >
                                @include("component.input._select",[
                                    "id"=>"classification_id",
                                    "label"=>"طبقه بندی ",
                                     "option"=>$classification_option["items"],
                                    "val"=>$classification_option["value"],
                                    "text"=>$classification_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="w-100"><br/></div>


                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
                        <button type="submit" class="btn btn-primary"> دریافت فایل اکسل</button>

                        <input type="hidden" id="leading_false" value="1">
                    </form>
                </div>

            </div>
        </div>

    </div>
@endsection
@section("styles")

    @include("component.input.datepicker._script")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


@section("scripts")
    @include("component.script_function.get_new_option")
    <script>
        $('#form1').validate({
            rules: {
                "start_date_value": "required",
                "end_date_value": "required",
            }
        });
        $("#switch-breaking_by_classification").change(function (){
            classification();
        })
        $("#goods_kind_id").change(function () {

            get_new_option(
                0,
                $("#goods_kind_id").val(),
                " طبقه بندی کالایی",
                "classification_id",
                "goods_kind_classification_group_option",
            )
        })
        function classification(){
            if($("#switch-breaking_by_classification").is(":checked")){
                $(".classification_group").css("display","")
            }
            else {
                $(".classification_group").css("display","none")
            }
        }
        classification();
    </script>
@endsection
