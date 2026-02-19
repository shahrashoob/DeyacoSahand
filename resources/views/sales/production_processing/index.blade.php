@extends('layouts.admin._master')
@section("page_header_title","داشبورد فروش - سفارش  ".$order_list->order->code())

@section('content')
    <div class="row">

        <div class="col-md-12">
            @include("sales.production_processing._planing_info")
        </div>
        <div class="col-sm-12">


            <form id="form1" autocomplete="off"
                  action="{{route("sales.production_processing.submit",[$order_list])}}"
                  method="post"
                  novalidate="novalidate">
                @csrf

                @if($order_list->production_card_id == null)


                    @if($allow_process)
                        <div class="card">
                            <div class="card-header">
                                <h5>صدور کارت تولید سطح 1</h5>
                            </div>
                            <div class="card-block">
                                <div class="row">
                                    @include("component.input._lable",[
                                        "label"=>"  کالا ",
                                        "value"=>$order_list->product->fullCaption(),

                                        ])

                                    @include("component.input._lable",[
                                        "label"=>"  نوع بسته بندی ",
                                        "value"=>$order_list->getPackingType("list"),

                                        ])

                                    @include("component.input._lable",[
                                        "label"=>"  اولویت ",
                                        "value"=>$order_list->order->priority->caption,

                                        ])


                                    @include("component.input._lable",["id"=>"","lable"=>"حداکثر تاریخ تحویل  ","value"=>$max_delivery_datetime1_fa])


                                    @include("component.input._number",["id"=>"amount","class_col"=>"col-md-4",'label'=>"مقدار (واحد اصلی)  ","value"=>$order_list->amount])

                                    @if($order_list->product->has_batch_with_packaging_number("exists"))
                                        <div class="w-100"></div>
                                        @include("component.input._number",["id"=>"number_of_packing_form","class_col"=>"col-md-4",'label'=>"تعداد بسته بندی  ","value"=>$order_list->number_of_packing_form])
                                    @endif
                                    <div class="col-md-12">
                                        <a class="btn btn-outline-dark"
                                           href="{{route("sales.dashboard.view_order",$order_list->order)}}">بارگشت</a>
                                        <button type="submit" class="btn btn-primary"
                                                onclick="return confirm('آیا تعداد انتخاب شده اطمینان دارید؟')"
                                                id="btn_confirm">تایید و ادامه
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                @else
                    <div class="card">
                        <div class="card-header">
                            <h5> کارت تولید سطح 1: {{$order_list->production->serial()}}  </h5>
                        </div>
                        <div class="card-block">
                            <div class="row">
                                @include("component.input._lable",[
                                    "label"=>"  سریال کارت  ",
									"url"=>$order_list->production->product->supply_type_id ==1?

										route("production.dashboard.view_card",$order_list->production):
										(
											$order_list->production->product->supply_type_id ==3?
										    route("contractor.admin.dashboard.view_card",$order_list->production):"#"
										    )
									,
                                    "value"=>$order_list->production->serial(),

                                    ])

                                @include("component.input._lable",[
                                    "label"=>"  کالا ",
                                    "value"=>$order_list->production->product->fullCaption(),

                                    ])

                                @include("component.input._lable",[
                                    "label"=>"  نوع بسته بندی ",
                                    "value"=>$order_list->production->getPackingType("list"),

                                    ])

                                @include("component.input._lable",[
                                    "label"=>"  اولویت ",
                                    "value"=>$order_list->production->priority->caption,

                                    ])
                                @include("component.input._lable",[
                                    "label"=>"  وضعیت ",
                                    "value"=>$order_list->production->getStatus(),

                                    ])

                                @include("component.input._lable",["id"=>"","lable"=>"حداکثر تاریخ تحویل  ","value"=>$order_list->production->get_max_delivery_date()])


                                @include("component.input._lable",["id"=>"","lable"=>"مقدار کارت تولید  ","value"=>$order_list->production->number." ".$order_list->product->unit->caption])

                                @if($order_list->production->product->has_batch_with_packaging_number("exists"))
                                    @include("component.input._lable",["id"=>"number_of_packing_form","class_col"=>"col-md-4",'label'=>"تعداد بسته بندی  ","value"=>$order_list->production->number_of_packing_form])
                                @endif

                            </div>
                        </div>
                    </div>

                    @foreach($level2_production_list as $item)
                        <div class="card">
                            <div class="card-header">
                                @if(in_array($item->product_id, $material_level2_ids))
                                    <h5> کارت تولید سطح 2 :{{$item->serial()}} </h5>
                                @else
                                    <h5> کارت تولید سطح 3 :{{$item->serial()}} </h5>
                                @endif

                            </div>
                            <div class="card-block">
                                <div class="row">
                                    @include("component.input._lable",[
                                    "label"=>"  سریال کارت  ",
									"url"=>$item->product->supply_type_id ==1?

										route("production.dashboard.view_card",$item):
										(
											$item->product->supply_type_id ==3?
										    route("contractor.admin.dashboard.view_card",$item):"#"
										    )
									,
                                    "value"=>$item->serial(),

                                    ])
                                    @include("component.input._lable",[
                                        "label"=>"  کالا ",
                                        "value"=>$item->product->fullCaption(),

                                        ])

                                    @include("component.input._lable",[
                                        "label"=>"  نوع بسته بندی ",
                                        "value"=>$item->packing_type->caption??"",

                                        ])

                                    @include("component.input._lable",[
                                        "label"=>"  اولویت ",
                                        "value"=>$item->priority->caption,

                                        ])

                                    @include("component.input._lable",[
                                        "label"=>"  وضعیت ",
                                        "value"=>$item->getStatus(),

                                        ])

                                    @include("component.input._lable",["id"=>"","lable"=>"حداکثر تاریخ تحویل  ","value"=>$item->get_max_delivery_date()])


                                    @include("component.input._lable",["id"=>"","lable"=>"مقدار کارت تولید  ","value"=>$item->number])

                                    @if($item->product->has_batch_with_packaging_number("exists"))
                                        @include("component.input._lable",["id"=>"number_of_packing_form","class_col"=>"col-md-4",'label'=>"تعداد بسته بندی  ","value"=>$item->number_of_packing_form])
                                    @endif

                                </div>
                            </div>
                        </div>

                    @endforeach

                    @if( $allow_process)
                        @if(count($product_option["items"]) > 1)
                            <div class="card">
                                <div class="card-header">
                                    <h5>صدور کارت تولید سطح 2 </h5>
                                </div>
                                <div class="card-block">
                                    <div class="row">

                                        @include("component.input._lable",[
                                            "label"=>"  اولویت ",
                                            "value"=>$order_list->production->priority->caption,

                                            ])

                                        @include("component.input._lable",["id"=>"","lable"=>"حداکثر تاریخ تحویل  ","value"=>$order_list->production->get_max_delivery_date()])

                                        <div class="col-md-4">
                                            @include("component.input._aotocomplet2",[
                                                "id"=>"material_id",
                                                "label"=>" انتخاب کالا ",
                                                "option"=>$product_option["items"],
                                                "val"=>$product_option["value"],
                                                "text"=>$product_option["text"],
                                                "class_col"=>""
                                                ])
                                        </div>

                                        <div class="w-100"></div>
                                        @include("component.input._number",["id"=>"amount","class_col"=>"col-md-4",'label'=>"تعداد ( واحد اصلی)  ","value"=>""])

                                        <div class="col-md-12">
                                            <a class="btn btn-outline-dark"
                                               href="{{route("sales.dashboard.view_order",$order_list->order)}}">بارگشت</a>
                                            <button type="submit" class="btn btn-primary"
                                                    onclick="return confirm('آیا تعداد انتخاب شده اطمینان دارید؟')"
                                                    id="btn_confirm">تایید و ادامه
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif



                @endif
            </form>
            @if( $allow_process)
                @if(count($product_option_level3["items"]) > 1)
                    <form id="form3" autocomplete="off"
                          action="{{route("sales.production_processing.submit_3",[$order_list])}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf
                    <div class="card">
                        <div class="card-header">
                            <h5>صدور کارت تولید سطح 3 </h5>
                        </div>
                        <div class="card-block">
                            <div class="row">

                                @include("component.input._lable",[
                                    "label"=>"  اولویت ",
                                    "value"=>$order_list->production->priority->caption,

                                    ])

                                @include("component.input._lable",["id"=>"max_delivery_datetime3","lable"=>"حداکثر تاریخ تحویل  ","value"=>$order_list->production->get_max_delivery_date()])

                                <div class="col-md-4">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"material_id_level_3",
                                        "label"=>" انتخاب کالا ",
                                        "option"=>$product_option_level3["items"],
                                        "val"=>$product_option_level3["value"],
                                        "text"=>$product_option_level3["text"],
                                        "class_col"=>""
                                        ])
                                </div>

                                <div class="w-100"></div>
                                @include("component.input._number",["id"=>"amount_level3","class_col"=>"col-md-4",'label'=>"تعداد ( واحد اصلی)  ","value"=>""])

                                <div class="col-md-12">
                                    <a class="btn btn-outline-dark"
                                       href="{{route("sales.dashboard.view_order",$order_list->order)}}">بارگشت</a>
                                    <button type="submit" class="btn btn-primary"
                                            onclick="return confirm('آیا تعداد انتخاب شده اطمینان دارید؟')"
                                            id="btn_confirm">تایید و ادامه
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                @endif
            @endif
        </div>
        @if($allow_process)
            <div class="col-md-12">
                @include("sales.production_processing._over_production")


            </div>
        @endif

        @include("sales.public._log",["event_id"=>35099,"order"=>$order_list->order,"is_customer"=>false,"title"=>"لیست کارت های مازاد تولید"])

        <a class="btn btn-outline-dark"
           href="{{route("sales.dashboard.view_order",$order_list->order)}}">بارگشت</a>
    </div>

@endsection
@section("styles")
    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "amount": "required",
                "material_id_auto": "required",
                "number_of_packing_form": "required",
                "over_max_delivery_datetime1": "required",

            }
        });
        $('#form3').validate({
            rules: {
                "material_id_level_3_auto": "required",

                "amount_level3": "required",
            }
        });

        $('#form_over').validate({
            rules: {
                "over_amount": "required",
                "over_priority_id": "required",
                "over_number_of_packing_form": "required",
            }
        });
    </script>
@endsection
