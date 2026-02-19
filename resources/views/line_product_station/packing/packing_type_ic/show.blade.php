@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> مشخصات نوع بسته بندی {{$packing_type["caption"]}} </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">

                            @include("component.input._lable",["id"=>"caption",'label'=>"نام نوع بسته بندی","value"=>$packing_type["caption"]])


                            @include("component.input._lable",["id"=>"label_caption","label"=>"عنوان لیبل (system= نام سامانه)","value"=>$packing_type["label_caption"]])
                            @include("component.input._lable",["id"=>"weight","label"=>"وزن بسته بندی بدون حامل  وکالا(کیلوگرم)","value"=>$packing_type["weight"]])


                            @include("component.input._lable",["id"=>"max_amount_of_production_form_separately","label"=>"حداکثر مقدار هر آیتم  فرم تولید در زمان استخراج به صورت مجزا","value"=>$packing_type["max_amount_of_production_form_separately"]])
                            @include("component.input._lable",["id"=>"max_row_to_display_sub_packing_in_print","label"=>"حداکثر ردیف برای نمایش بسته بندی های فرعی در پرینت","value"=>$packing_type["max_row_to_display_sub_packing_in_print"]])

                            @include("component.input._lable",["id"=>"weight_error_percentage","label"=>"درصد خطای وزن بسته بندی","value"=>$packing_type["weight_error_percentage"]])


                            @include("component.input._lable",["id"=>"length",'label'=>"  طول  (میلیمتر)","value"=>$packing_type["length"]])
                            @include("component.input._lable",["id"=>"width",'label'=>" عرض  (میلیمتر) ","value"=>$packing_type["width"]])
                            @include("component.input._lable",["id"=>"height",'label'=>" ارتفاع (میلیمتر)","value"=>$packing_type["height"]])



                            <div class="col-md-12">
                                @foreach($packing_type_layer as $layer)
                                    لایه {{$layer["layer_code"]}}:
                                    {{--                                        نوع بسته بندی:--}}
                                    {{--                                       (  {{$layer->id}})--}}
                                    @if($layer["layer_code"] == 1 && count($packing_type_layer) > 1)
                                        {{$first_packing_type["code"]}}
                                        -  {{$first_packing_type["caption"]}}
{{--                                        (میانگین وزن حامل : {{$carrier_type_list[$layer["carrier_type_id"]]["average_weight"]}})--}}
                                    @else
                                        @if(isset($carrier_type_list[$layer["carrier_type_id"]]))
                                        {{$carrier_type_list[$layer["carrier_type_id"]]["id"]}} -
                                        {{$carrier_type_list[$layer["carrier_type_id"]]["caption"]}}
                                            (میانگین وزن حامل : {{$carrier_type_list[$layer["carrier_type_id"]]["average_weight"]}})
                                        @else
                                        فاقد حامل
                                        @endif
                                    @endif
                                    {{--                                            <a href="{{route("line_product_station.packing.packing_type.remove_layer",[$item,$layer->layer_code])}}"--}}
                                    {{--                                               onclick="return confirm('آیا از حذف اطمینان دارید؟')"--}}
                                    {{--                                               class="text-danger"><i class="fa fa-trash"></i> </a>--}}

                                    ,
                                @endforeach
                            </div>

                        </div>


                    </div>
                </div>
            </div>


        </div>


@if($first_packing_type)

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{$first_packing_type["code"]}}-    {{$first_packing_type["caption"]}} (بسته بندی اولین لایه) </h5>
                    </div>
                    <div class="card-block">


                        <div class="row">

                            @include("component.input._lable",["id"=>"caption",'label'=>"نام نوع بسته بندی","value"=>$first_packing_type["caption"]])


                            @include("component.input._lable",["id"=>"label_caption","label"=>"عنوان لیبل (system= نام سامانه)","value"=>$first_packing_type["label_caption"]])
                            @include("component.input._lable",["id"=>"weight","label"=>"وزن بسته بندی بدون حامل  وکالا(کیلوگرم)","value"=>$first_packing_type["weight"]])


                            @include("component.input._lable",["id"=>"max_amount_of_production_form_separately","label"=>"حداکثر مقدار هر آیتم  فرم تولید در زمان استخراج به صورت مجزا","value"=>$first_packing_type["max_amount_of_production_form_separately"]])
                            @include("component.input._lable",["id"=>"max_row_to_display_sub_packing_in_print","label"=>"حداکثر ردیف برای نمایش بسته بندی های فرعی در پرینت","value"=>$first_packing_type["max_row_to_display_sub_packing_in_print"]])

                            @include("component.input._lable",["id"=>"weight_error_percentage","label"=>"درصد خطای وزن بسته بندی","value"=>$first_packing_type["weight_error_percentage"]])


                            @include("component.input._lable",["id"=>"length",'label'=>"  طول  (میلیمتر)","value"=>$first_packing_type["length"]])
                            @include("component.input._lable",["id"=>"width",'label'=>" عرض  (میلیمتر) ","value"=>$first_packing_type["width"]])
                            @include("component.input._lable",["id"=>"height",'label'=>" ارتفاع (میلیمتر)","value"=>$first_packing_type["height"]])



                            <div class="col-md-12">
                                @foreach($first_packing_type_layer as $layer)
                                    لایه {{$layer["layer_code"]}}:
                                    {{--                                        نوع بسته بندی:--}}
                                    {{--                                       (  {{$layer->id}})--}}
                                    @if($layer["layer_code"] == 1 && count($first_packing_type_layer) > 1)
                                        {{$secound_packing_type["code"]}}
                                        -  {{$secound_packing_type["caption"]}}
{{--                                        (میانگین وزن حامل : {{$carrier_type_list[$layer["carrier_type_id"]]["average_weight"]}})--}}
                                    @else
                                        @if(isset($carrier_type_list[$layer["carrier_type_id"]]))
                                            {{$carrier_type_list[$layer["carrier_type_id"]]["id"]}}
                                            {{$carrier_type_list[$layer["carrier_type_id"]]["caption"]}}
                                            (میانگین وزن حامل : {{$carrier_type_list[$layer["carrier_type_id"]]["average_weight"]}})
                                        @else
                                            فاقد حامل
                                        @endif
                                    @endif
                                    {{--                                            <a href="{{route("line_product_station.packing.packing_type.remove_layer",[$item,$layer->layer_code])}}"--}}
                                    {{--                                               onclick="return confirm('آیا از حذف اطمینان دارید؟')"--}}
                                    {{--                                               class="text-danger"><i class="fa fa-trash"></i> </a>--}}

                                    ,
                                @endforeach
                            </div>

                        </div>


                    </div>
                </div>
            </div>



        </div>
@endif
    <div class="row">
        <div class="col-md-12">
            <a href="{{route("line_product_station.packing.packing_type_ic.index")}}"
               class="btn btn-outline-dark">بازگشت</a>


        </div>
    </div>
@endsection
