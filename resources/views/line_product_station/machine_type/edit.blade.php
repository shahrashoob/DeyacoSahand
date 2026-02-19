@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5> ویرایش ماشین {{$machine_type->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("line_product_station.machine_type.update",$machine_type)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"code",'label'=>"کد ماشین","value"=>$machine_type->getCode(),"readonly"=>1])
                            @include("component.input._text",["id"=>"caption",'label'=>"عنوان ماشین ","value"=>$machine_type->caption,"autofocus"=>1])
                            @include("component.input._text",["id"=>"lot_effective_code",'label'=>"کد موثر لات","value"=>$machine_type->lot_effective_code])
                            @include("component.input._number",["id"=>"warehouse_max_capacity",'label'=>"حداکثر ظرفیت انبارک گروه ماشین (ساعت کاری انبار)","value"=>$machine_type->warehouse_max_capacity])
                            @include("component.input._number",["id"=>"warehouse_request_point",'label'=>"نقطه سفارش (درخواست) انبارک ماشین (ساعت کاری انبار)","value"=>$machine_type->warehouse_request_point])
                            @include("component.input._number",["id"=>"number_of_contour_in_minute",'label'=>"کارکرد کنتور اصلی در دقیقه ","value"=>$machine_type->number_of_contour_in_minute])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"ic",
                                    "label"=>" مرکز هزینه   ",
                                    "option"=>$cost_center_option["items"],
                                    "val"=>$cost_center_option["value"],
                                    "text"=>$cost_center_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>


                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"machine_module_type_id",
                                    "label"=>" ماژول های  ماشین ",
                                    "option"=>$machine_module_type_option["items"],
                                    "val"=>$machine_module_type_option["value"],
                                    "text"=>$machine_module_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>


                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"machine_type_consumption_type_id",
                                    "label"=>" روش ثبت تراکنش های مصرف ",
                                    "option"=>$machine_type_consumption_type_option["items"],
                                    "val"=>$machine_type_consumption_type_option["value"],
                                    "text"=>$machine_type_consumption_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت   ",
                                    "option"=>$status_option["items"],
                                    "val"=>$machine_type->active_status->id??"",
                                    "text"=>$machine_type->active_status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>



                        </div>

                        <a href="{{route("line_product_station.machine_type.index",$machine_type->station_id)}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

                </div>
            </div>
        </div>


        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5> ویژگی های {{$machine_type->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form2"
                          action="{{route("line_product_station.machine_type.property_update",$machine_type)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @foreach($machine_type->machine_property()  as $item)

                                @if($item->field_type_id!=3)
                                    @include("component.input._text",["id"=>"m_p_".$item->id,'label'=>$item->caption,"value"=>$machine_type->get_property_value($item->id)])
                                @else
                                    @include("component.input._select",["id"=>"m_p_".$item->id,'label'=>$item->caption,"option"=>$option[$item->id]["items"]])
                                @endif

                            @endforeach

                        </div>

                        <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

                    </form>

                </div>
            </div>
        </div>

        @include("line_product_station.machine_type.warehouse_handling._goods_kind_warehouse_handling",[
          "warehouse_type_id"=>3,
          "belonging_to_id"=>$machine_type->id,
          "url"=>route("line_product_station.machine_type.warehouse_handling_update",$machine_type
          )])
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
                "caption": "required",
                "lot_effective_code": "required",
                "ic_auto": "required",
                "active_status_id": "required",
                "position_number": "required",
                "section_number": "required",
                "band_number": "required",
                "active_status_id_auto": "required",
                "machine_module_type_id_auto": "required",
                "machine_type_consumption_type_id": "required",
                "number_of_contour_in_minute": "required",
                "warehouse_handling_time_limit": {required: true, min: 1}

            }
        });
        $('#form2').validate({
            rules: {
                @foreach($machine_type->machine_property()  as $item)
                @if($item->field_type_id!=3)
                @php echo 'm_p_'.$item->id.':{required:true,min:'.$item->min_value.",max:".$item->max_value.' },';@endphp
                @else
                @php echo 'm_p_'.$item->id.':{required:true },';@endphp

                @endif

                @endforeach
            }
        });
    </script>
@endsection
