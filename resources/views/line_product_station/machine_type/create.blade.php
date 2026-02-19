@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <form id="form1" action="{{route("line_product_station.machine_type.store",$station)}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن گروه ماشین به ایستگاه {{$station->caption}}</h5>
                </div>
                <div class="card-block">


                        <div class="row">
                            @include("component.input._text",["id"=>"caption",'label'=>"نام گروه ماشین","value"=>"","autofocus"=>1])
                            @include("component.input._number",["id"=>"section_number",'label'=>"تعداد کل سکشن","value"=>"1"])
                            @include("component.input._number",["id"=>"position_number",'label'=>"تعداد کل چشمه  ","value"=>"1"])
                            @include("component.input._text",["id"=>"lot_effective_code",'label'=>"کد موثر لات","value"=>""])


                            @include("component.input._text",["id"=>"count",'label'=>"تعداد ماشین ","value"=>1])
                            @include("component.input._number",["id"=>"warehouse_max_capacity",'label'=>"حداکثر ظرفیت انبارک گروه ماشین (ساعت کاری انبار)","value"=>0])
                            @include("component.input._number",["id"=>"warehouse_request_point",'label'=>"نقطه سفارش (درخواست) انبارک ماشین (ساعت کاری انبار)","value"=>0])
                            @include("component.input._number",["id"=>"number_of_contour_in_minute",'label'=>"کارکرد کنتور اصلی در دقیقه ","value"=>""])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"ic",
                                    "label"=>" مرکز هزینه   ",
                                    "option"=>$cost_center_option["items"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"machine_module_type_id",
                                    "label"=>" ماژول های  ماشین ",
                                    "option"=>$machine_module_type_option["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._select",[
                                    "id"=>"machine_type_consumption_type_id",
                                    "label"=>" روش ثبت تراکنش های مصرف ",
                                    "option"=>$machine_type_consumption_type_option["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت  فعال بودن ",
                                    "option"=>$status_option["items"],
                                    "val"=>$station->active_status->id??"",
                                    "text"=>$station->active_status->caption??"",
                                    "class_col"=>""
                                    ])
                            </div>
                        </div>

                        <a href="{{route("line_product_station.machine_type.index",$machine_type->station_id)}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> افزودن</button>



                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5>ویژگی های گروه ماشین </h5>
                </div>
                <div class="card-block">

                    <div class="row">

                        @foreach($machine_type->machine_property()  as $item)

                            @if($item->field_type_id!=3)
                                @include("component.input._text",["id"=>"m_p_".$item->id,'label'=>$item->caption,"value"=>$machine_type->get_property_value($item->id)])
                            @else
                                @include("component.input._select",["id"=>"m_p_".$item->id,'label'=>$item->caption,"option"=>$option[$item->id]["items"]])
                            @endif


                        @endforeach

                    </div>


                </div>
            </div>
        </div>

    </div>
    </form>
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
