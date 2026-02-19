@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","کارتابل برنامه ریزی ")
@section("content")

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5> ( ویژه دوره پیاده سازی) برگ دستور تولید (پیمان)</h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("utility.planing.production_order_demo_confirm")}}"
                          method="post"
                          novalidate="novalidate" autocomplete="off" to>
                        @csrf

                        <div class="row">
                            @include("component.input._lable",[
                                "label"=>"  کالا ",
                                "value"=>$product->fullCaption(),

                                ])
                            @include("component.input._hidden",[
                                "id"=>"product_id",
                                "value"=>$product->id,
                                ])

                            @include("component.input._lable",[
                                "label"=>"  اولویت ",
                                "value"=>$priority->caption,

                                ])
                            @include("component.input._hidden",[
                                "id"=>"priority_id",
                                "value"=>$priority->id,
                                ])


                            @include("component.input._lable",[
                                "label"=>"  نوع کارت ",
                                "value"=>$production_type->caption,

                                ])
                            @include("component.input._hidden",[
                                "id"=>"production_type_id",
                                "value"=>$production_type->id,
                                ])


                            @include("component.input._lable",["id"=>"","lable"=>"تعداد ( واحد اصلی) ","value"=>$carton])
                            @include("component.input._hidden",["id"=>"carton","value"=>$carton])

                            {{--                            حداقل یکی از مسیرها وابسته به بچ تعداد بسته بندی است.--}}
                            @if($line_product_station_for_number_of_packing_type)
                                @include("component.input._number",["id"=>"number_of_packing_form","lable"=>
                                    "تعداد بسته بندی(","other_content"=>"بسته بندی از نوع ".
                                    $line_product_station_for_number_of_packing_type->material_packing_type_dependent_to_batch->code." و شامل کالای ".
                                     $line_product_station_for_number_of_packing_type->material_dependent_to_batch->caption.
                                     ") ","value"=>""])
                            @endif


                            @if($production_type->id == 1 && $product->goods_kind->production_algorithm_type_id == 1)
                                @include("component.input._lable",["id"=>"","lable"=>"شماره سفارش","value"=>$order_code])
                                @include("component.input._hidden",["id"=>"order_code","lable"=>"شماره سفارش","value"=>$order_code])

                                @include("component.input._lable",["id"=>"","lable"=>"سری سفارش","value"=>$series])
                                @include("component.input._hidden",["id"=>"series","lable"=>"سری سفارش","value"=>$series])
                                @if($production_card)
                                    @include("component.input._lable",["id"=>"","lable"=>"کارت تولید سطح بالا","value"=>$production_card->serial??""])
                                    @include("component.input._lable",["id"=>"","lable"=>"کالای کارت تولید سطح بالا","class_col"=>"col-md-12","value"=>$production_card->product->fullCaption()])
                                @endif
                                @include("component.input._hidden",["id"=>"parent_production_id","value"=>$production_card->id??0])

                                @include("component.input._lable",["id"=>"","lable"=>"حداکثر تاریخ تحویل  ","value"=>$max_delivery_datetime1_fa])
                                @include("component.input._hidden",["id"=>"max_delivery_datetime1","value"=>$max_delivery_datetime1])
                            @endif

                            @switch(count($packing_type_list))
                                @case (0)
                                    <div class="alert alert-danger">
                                        با توجه به نوع کارت تولید (تولیدی/نمونه گیری) و روش برنامه ریزی تولید هیچ نوع
                                        بسته بندی پیشنهادی برای کالا وجود ندارد.
                                        <br/>
                                        لطفا نوع بسته بندی های مجاز کالا ({{$product->caption}}) را بررسی فرمایید، در صورتی که کالا قابلیت فروش دارد،
                                        می بایستی حداقل یک بسته بندی جهت فروش انتخاب شده باشد.
                                    </div>
                                    <div class="w-100"></div>
                                    @break
                                @case(1)

                                    @include("component.input._lable",["id"=>"","lable"=>"بسته بندی مجاز ","value"=>$packing_type_list[0]->packing_type->caption])
                                    @include("component.input._hidden",["id"=>"packing_type_ids[".$packing_type_list[0]->packing_type->id."]","value"=>$packing_type_list[0]->packing_type->id])

                                    @if($packing_type_list[0]->packing_type->packaging_forms_include_brand)
                                        <br/>
                                       با توجه به اینکه بسته بندی شامل لوگو می باشد، لطفا مقدار لوگو را وارد نمایید (
                                        {{$packing_type_list[0]->packing_type->normal_amount_unit_type_id==1?$product->unit->caption:$product->sub_unit->caption}}):
                                        <br/>
                                        <input type="number" name="normal_amount"  required width="60px">
                                    @endif
                                    @break
                                @default
                                    <div class="col-md-12">
                                        <table>
                                            <tr>

                                                <td>بسته بندی های مجاز:</td>
                                                <td></td>
                                            </tr>
                                            @php $has_logo=false; @endphp
                                            @foreach($packing_type_list as $item)
                                                <tr>
                                                    <td>


                                                    </td>
                                                    <td>
                                                        <input type="checkbox" value="{{$item->packing_type->id}}"
                                                               name="packing_type_ids[{{$item->packing_type->id}}]">
                                                        {{$item->packing_type->code}} -
                                                        {{$item->packing_type->caption}}
                                                    </td>
                                                    @if($item->packing_type->packaging_forms_include_brand)
                                                        @php $has_logo=true; @endphp
                                                    @endif
                                                </tr>
                                            @endforeach
                                            @if($has_logo)
                                                <tr>
                                                    <td colspan="2">
                                                        <br/>
                                                        با توجه به اینکه بسته بندی شامل لوگو می باشد، لطفا مقدار لوگو را وارد نمایید (
                                                        {{$packing_type_list[0]->packing_type->normal_amount_unit_type_id==1?$product->unit->caption:$product->sub_unit->caption}}):
                                                        <br/>
                                                        <input type="number" name="normal_amount"  required width="60px">
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                        <br/>
                                        <br/>
                                    </div>
                        </div>
                        @endswitch
                        <div class="row">
                            <div class="col-md-12">

                                @if($production_type->id == 1 && $product->goods_kind->production_algorithm_type_id == 1 && !$order_exists)
                                    <div class="alert alert-warning">
                                        با توجه به اینکه سفارش شماره
                                        {{$order_code}}
                                        در سیستم وجود ندارد، در صورت تایید فرم، یک سفارش جدید با شماره
                                        {{$order_code}}
                                        ایجاد می گردد.
                                    </div>
                                @endif

                                <div class="col-md-12">
                                    @include("component.lightbox._lightbox",["src"=>"upload/product/".($product->image->filename??'')])
                                </div>
                                <a href="{{route("utility.planing.production_order_demo")}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                @if(count($packing_type_list)>0)
                                    <button type="submit" class="btn btn-primary" id="btn_confirm">تایید و ادامه
                                    </button>
                                @endif
                            </div>
                        </div>
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
    <script>

        @if(count($packing_type_list)>1)
        $("#btn_confirm").click(function () {

            return confirm("آیا از انتخاب بسته بندی مجاز  اطمینان دارید؟")
            // if ($('input[name=packing_type_id]:checked').length) {
            //     return confirm("آیا از انتخاب بسته بندی مجاز  اطمینان دارید؟")
            // } else {
            //     alert("لطفا یک بسته بندی مجاز را انتخاب نمایید.");
            //     return false;
            // }
        });
        @endif
        $('#form1').validate({
            rules: {
                "packing_type_id": "required",
                "number_of_packing_form": "required",
                "normal_amount": "required",

            }
        });
    </script>
@endsection
