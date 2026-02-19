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
                        <h5>لیست بسته بندی های

                            <b>{{$orderConsumedProduct->material->caption}}</b>
                            برای
                            {{$orderConsumedProduct->product->caption}}
                        </h5>
                    </div>
                    <div class="card-block overflow-auto">
                        @if(count($orderConsumedProduct->get_order_packing_forms())>0)
                            <table class="table table-hover center">
                                <thead>
                                <tr>
                                    <th class="center">ردیف</th>

                                    <th>کد بسته بندی</th>
                                    <th>کد نوع بسته بندی</th>
                                    <th>تعداد بسته بندی فرعی</th>
                                    <th>درجه</th>
                                    <th>لات</th>
                                    <th>
                                        {{$orderConsumedProduct->material->unit->measurement ." ".($orderConsumedProduct->material->unit->weight_conversion_rate!=0?"خالص":"")}}

                                    </th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($orderConsumedProduct->get_order_packing_forms() as $order_packing_form)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>{{$order_packing_form->packing_form_code}}</td>
                                        <td title="{{$order_packing_form->packing_type->caption}}"><a href="#">{{$order_packing_form->packing_type->code}}</a> </td>
                                        <td>{{$order_packing_form->sub_packing_form_number}}</td>
                                        <td>{{$order_packing_form->degree->caption}}</td>
                                        <td>{{$order_packing_form->lot_number_code}}</td>
                                        <td>{{$order_packing_form->amount}}</td>
                                        <td>
                                            <a href="{{route("customer_group.buy.order_packing_form.delete_order_packing_form",[$order,$order_packing_form])}}" class="text-danger">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        <form id="form1" style="display: inline"
                              action="{{route("customer_group.buy.order_packing_form.submit_new_packing_form",[$order,$orderConsumedProduct])}}"
                              method="post"
                              autocomplete="off">
                            @csrf

                            <div class="row">
                                @include("component.input._text",["id"=>"packing_form_code", "lable"=>"کد بسته بندی","value"=>"","class_col"=>"col-md-2"])


                                @include("component.input._select",[
                                    "id"=>"degree_id",
                                    "label"=>"درجه",
                                    "option"=>$degree_option["items"],
                                    "val"=>$degree_option["value"],
                                    "text"=>$degree_option["text"],
                                    "class_col"=>"col-md-2"
                                    ])

                                @include("component.input._select",[
                                   "id"=>"packing_type_id",
                                   "label"=>"نوع بسته بندی",
                                   "option"=>$packing_type_option["items"],
                                   "val"=>$packing_type_option["value"],
                                   "text"=>$packing_type_option["text"],
                                   "class_col"=>"col-md-2"
                                   ])
                                @include("component.input._text",["id"=>"sub_packing_form_number", "lable"=>"تعداد بسته بندی فرعی","value"=>"","class_col"=>"col-md-2"])
                                @include("component.input._text",["id"=>"lot_number_code", "lable"=>"لات","value"=>"","class_col"=>"col-md-2"])
                                @include("component.input._text",["id"=>"amount", "lable"=>$orderConsumedProduct->material->unit->measurement,"value"=>"","class_col"=>"col-md-2"])

                                <div class="col-md-12">

                                    <button type="submit" class="btn btn-primary">ثبت بسته بندی</button>
                                </div>
                            </div>

                        </form>


                    </div>
                </div>
            </div>
        </div>
    @endif


    @foreach($order->consumedProduct as $item)

        @if(!$orderConsumedProduct || $item->id != $orderConsumedProduct->id)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><h5>
                                <a href="{{route("customer_group.buy.order_packing_form.index",[$order,$item])}}">
                                    لیست بسته بندی های

                                    <b>{{$item->material->caption}}</b>
                                    برای
                                    {{$item->product->caption}}

                                </a></h5>
                        </div>

                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <div class="row center">
        <div class="col-md-12">
            <a href="{{route("customer_group.buy.shopping_cart",$order)}}"
               class="btn btn-outline-dark">
                بازگشت </a>
            <a href="{{route("customer_group.buy.address",$order)}}"
               class="btn btn-outline-primary">
                تایید و ادامه </a>

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
                packing_form_code: "required",
                lot_number_code: "required",
                amount: "required",

            }
        });

        var has_sub_packing_type =@php echo json_encode($has_sub_packing_type); @endphp;

        $("#packing_type_id").change(function (){


            if(has_sub_packing_type[$(this).val()]){
                $("#sub_packing_form_number").parent().css("display","");
            }
            else{
                $("#sub_packing_form_number").parent().css("display","none");
            }
        });

    </script>
@endsection