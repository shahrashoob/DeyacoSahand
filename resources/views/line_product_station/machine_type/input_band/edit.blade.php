@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت
                        <b>
                            {{ $machine_type_input_band->caption}}
                        </b>
                        از گروه ماشین
                        <b>
                            {{$machine_type->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("line_product_station.machine_type.input_band.update",$machine_type_input_band)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._number",["id"=>"input_line_number",'label'=>"حداکثر تعداد کل خط ورودی  ","value"=>$machine_type_input_band->input_line_number])

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"active_status_id",
                                    "label"=>" وضعیت   ",
                                    "option"=>$status_option["items"],
                                    "val"=>$status_option["value"],
                                    "text"=>$status_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md-6">
                                @include("component.input._checkbox",["id"=>"can_used_material_with_different_lot_per_production_card",'label'=>"آبا برای هر کارت تولید امکان مصرف ماده با لات های مختلف وجود دارد؟","checked"=>$machine_type_input_band->can_used_material_with_different_lot_per_production_card])

                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary"> ویرایش</button>

                    </form>

                </div>

            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست رسته ها و بسته بندی های مجاز
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.machine_type.input_band.goods_kind_update",$machine_type_input_band)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">

                            <div class="col-md-12 center">

                                <div class="w-100"></div>
                                <table class="table">
                                    @foreach($machine_type_input_band->goods_kinds as $goods_kind_item)
                                        <tr>
                                            <td width="50px"></td>
                                            <td>
                                                <h5 style="display: inline">{{$goods_kind_item->goods_kind->caption}}</h5>
                                                (تاثیر اشتراکی بر همبافت
                                                <b>{{$goods_kind_item->effect_is_shared?"دارد":"ندارد"}}</b>)
                                                <a href="{{route("line_product_station.machine_type.input_band.delete_goods_kind",[$machine_type_input_band,$goods_kind_item->goods_kind])}}"
                                                   onclick="confirm('آیا از حذف اطمینان دارید؟')"><i
                                                        class="fa fa-trash text-danger"></i> </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>امکان تزریق مواد اولیه به صورت دستی برای رسته کالایی وجود دارد؟

                                                <input type="radio"
                                                       name="allowing_raw_materials_to_be_injected_manually[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->allowing_raw_materials_to_be_injected_manually ?"checked":""}} value="1">
                                                بله
                                                <input type="radio"
                                                       name="allowing_raw_materials_to_be_injected_manually[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->allowing_raw_materials_to_be_injected_manually ?"":"checked"}} value="0">
                                                خیر
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>آیا بسته بندی های این رسته کالایی در لیست برگشت مواد اولیه قرار گیرد؟

                                                <input type="radio"
                                                       name="packing_form_in_return_raw_material_list[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->packing_form_in_return_raw_material_list ?"checked":""}} value="1">
                                                بله
                                                <input type="radio"
                                                       name="packing_form_in_return_raw_material_list[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->packing_form_in_return_raw_material_list ?"":"checked"}} value="0">
                                                خیر
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>آیا درخواست کالا از انبار توسط دستیار دیجیتال ارسال شود؟
<br/>
                                               ( در زمان تخصیص مقدار موجودی انبار برای کالاهای رسته کالایی چک شود؟)
                                                <input type="radio"
                                                       name="send_product_request_form_by_robot[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->send_product_request_form_by_robot ?"checked":""}} value="1">
                                                بله
                                                <input type="radio"
                                                       name="send_product_request_form_by_robot[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->send_product_request_form_by_robot ?"":"checked"}} value="0">
                                                خیر
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>آیا درخواست کالا به انبار توسط دستیار دیجیتال در ابتدا فعال باشد؟

                                                <input type="radio"
                                                       name="product_request_form_by_robot_is_enabled[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->product_request_form_by_robot_is_enabled ?"checked":""}} value="1">
                                                بله
                                                <input type="radio"
                                                       name="product_request_form_by_robot_is_enabled[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->product_request_form_by_robot_is_enabled ?"":"checked"}} value="0">
                                                خیر
                                            </td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td>آیا تایید فرم های ورود به انبار به صورت تجمیعی باشد؟

                                                <input type="radio"
                                                       name="warehouse_entry_confirmation_in_altogether[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->warehouse_entry_confirmation_in_altogether ?"checked":""}} value="1">
                                                بله
                                                <input type="radio"
                                                       name="warehouse_entry_confirmation_in_altogether[{{$goods_kind_item->goods_kind_id}}]"
                                                       {{$goods_kind_item->warehouse_entry_confirmation_in_altogether ?"":"checked"}} value="0">
                                                خیر
                                            </td>
                                        </tr>
{{--                                    <tr>--}}
{{--                                        <td></td>--}}
{{--                                        <td>--}}
{{--                                            محدودیت زمانی انبارگردانی انبارک ها  در رسته کالایی (روز)--}}
{{--                                            <input type="number"--}}
{{--                                                   name="warehouse_handling_time_limit[{{$goods_kind_item->goods_kind_id}}]"--}}
{{--                                                    value="{{$goods_kind_item->warehouse_handling_time_limit }}">--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
                                        <tr>
                                            <td>بسته بندی های مجاز {{$goods_kind_item->goods_kind->caption}}</td>
                                            <td>
                                                <table>
                                                    @foreach($goods_kind_item->goods_kind->packing_type as $item)
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" id="switch-data[{{$item->id}}]"
                                                                       name="data[{{$goods_kind_item->goods_kind->id}}][{{$item->id}}]"
                                                                    {{$machine_type_input_band->has_product_type_permission($item->id)?"checked='checked'":""}}
                                                                >
                                                            </td>
                                                            <td>{{$item->code}} </td>
                                                            <td>  {{$item->caption}}  </td>

                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                    </form>
                </div>


            </div>
        </div>
        <div>
            <a href="{{route("line_product_station.machine_type.input_band.index",$machine_type)}}"
               class="btn btn-outline-dark">بازگشت</a>
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
                "active_status_id_auto": "required",
                "line_input_number": "required",
            }
        });
        $('#form2').validate({
            rules: {
                "goods_kind_id_auto": "required",
                "line_input_number": "required",
            }
        });
    </script>
@endsection
