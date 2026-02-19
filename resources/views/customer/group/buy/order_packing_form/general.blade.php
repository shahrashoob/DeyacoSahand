@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")

    <div class="row">
        @include("customer.group.buy._order_factor_products")
    </div>
    @if($orderConsumedProduct)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>لیست فرم های تجمیعی ثبت شده
                            برای
                            <b>{{$orderConsumedProduct->material->caption}}</b>


                        </h5>
                    </div>
                    <div class="card-block overflow-auto">
                        @if($orderConsumedProduct->form_general_item)
                            <table class="table table-hover center">
                                <thead>
                                <tr>
                                    <th class="center">ردیف</th>
                                    <th>درجه</th>
                                    <th>لات</th>
                                    <th> نوع بسته بندی</th>
                                    <th>تعداد بسته بندی</th>
                                    <th>
                                        {{$orderConsumedProduct->material->unit->measurement ." ".($orderConsumedProduct->material->unit->weight_conversion_rate!=0?"خالص":"")}}
                                        (کل)
                                    </th>


                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp

                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>{{$orderConsumedProduct->form_general_item->degree->caption}}</td>
                                    <td>{{$orderConsumedProduct->form_general_item->lot_number->code}}</td>
                                    <td title="{{$orderConsumedProduct->form_general_item->packing_type->caption}}"><a
                                                href="#">{{$orderConsumedProduct->form_general_item->packing_type->code}}</a>
                                    </td>


                                    <td>{{$orderConsumedProduct->form_general_item->packing_form_number}}</td>
                                    <td>{{$orderConsumedProduct->form_general_item->amount}}</td>

                                    <th>
                                        <a class="text-danger" href="{{route("customer_group.buy.order_packing_form.delete_general_item",[$order,$orderConsumedProduct])}}">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </th>
                                </tr>

                                </tbody>
                            </table>

                        @else
                            <form id="form1" style="display: inline"
                                  action="{{route("customer_group.buy.order_packing_form.submit_new_packing_form",[$order,$orderConsumedProduct])}}"
                                  method="post"
                                  autocomplete="off">
                                @csrf

                                <div class="row">


                                    @include("component.input._select",[
                                        "id"=>"degree_id",
                                        "label"=>"درجه",
                                        "option"=>$degree_option["items"],
                                        "val"=>$degree_option["value"],
                                        "text"=>$degree_option["text"],
                                        "class_col"=>"col-md-2"
                                        ])
                                    @include("component.input._text",["id"=>"lot_number_code", "lable"=>"لات","value"=>"","class_col"=>"col-md-2"])


                                    @include("component.input._select",[
                                       "id"=>"packing_type_id",
                                       "label"=>"نوع بسته بندی",
                                       "option"=>$packing_type_option["items"],
                                       "val"=>$packing_type_option["value"],
                                       "text"=>$packing_type_option["text"],
                                       "class_col"=>"col-md-2"
                                       ])
                                    @include("component.input._text",["id"=>"packing_form_number", "lable"=>"تعداد بسته بندی ","value"=>"","class_col"=>"col-md-2"])
                                    @include("component.input._text",["id"=>"amount", "lable"=>$orderConsumedProduct->material->unit->measurement." کل","value"=>"","class_col"=>"col-md-2"])

                                    <div class="col-md-12">

                                        <button type="submit" class="btn btn-primary">ثبت فرم تجمیعی</button>
                                    </div>
                                </div>

                            </form>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif



    <div class="row center">
        <div class="col-md-12">
            <a href="{{route("customer_group.buy.shopping_cart",$order)}}"
               class="btn btn-outline-dark">
                بازگشت </a>
            @if($next_order_consumed)
                <a href="{{route("customer_group.buy.order_packing_form.index",[$order,$next_order_consumed])}}"
                   class="btn btn-outline-primary">
                    تایید و ادامه </a>
            @else
                <a href="{{route("customer_group.buy.address",$order)}}"
                   class="btn btn-outline-primary">
                    تایید و ادامه </a>
            @endif

        </div>
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
                degree_id: "required",
                packing_form_number: "required",
                packing_type_id: "required",
                lot_number_code: "required",
                amount: "required",

            }
        });


    </script>
@endsection