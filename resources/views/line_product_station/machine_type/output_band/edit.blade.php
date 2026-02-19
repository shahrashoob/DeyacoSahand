@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت
                        <b>
                            {{ $machine_type_output_band->caption}}
                        </b>
                        از گروه ماشین
                        <b>
                            {{$machine_type->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route("line_product_station.machine_type.output_band.update",$machine_type_output_band)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">

                            @include("component.input._number",["id"=>"output_line_number",'label'=>"حداکثر تعداد کل خط خروجی  ","value"=>$machine_type_output_band->output_line_number])

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
                                @include("component.input._aotocomplet2",[
                                    "id"=>"doff_algorithm_id",
                                    "label"=>" الگوریتم داف برای کالا تولید شده   ",
                                    "option"=>$algorithm_type_option["items"],
                                    "val"=>$algorithm_type_option["value"],
                                    "text"=>$algorithm_type_option["text"],
                                    "class_col"=>""
                                    ])
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
                          action="{{route("line_product_station.machine_type.output_band.goods_kind_update",$machine_type_output_band)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">

                            <div class="col-md-12 center">

                                <div class="w-100"></div>
                                <table class="table">
                                    @foreach($machine_type_output_band->goods_kinds as $goods_kind_item)
                                        <tr>
                                            <td width="50px"></td>
                                            <td>
                                                <h5 style="display: inline">{{$goods_kind_item->goods_kind->caption}}</h5>

                                                <a href="{{route("line_product_station.machine_type.output_band.delete_goods_kind",[$machine_type_output_band,$goods_kind_item->goods_kind])}}"
                                                   onclick="confirm('آیا از حذف اطمینان دارید؟')"><i
                                                            class="fa fa-trash text-danger"></i> </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>بسته بندی های مجاز {{$goods_kind_item->goods_kind->caption}}</td>
                                            <td>
                                                @foreach($goods_kind_item->goods_kind->packing_type as $item)
                                                    <input type="checkbox" id="switch-data[{{$item->id}}]"
                                                           name="data[{{$goods_kind_item->goods_kind->id}}][{{$item->id}}]"
                                                            {{$machine_type_output_band->has_product_type_permission($item->id)?"checked='checked'":""}}
                                                    >
                                                    <b> {{$item->caption}}  </b>
                                                    <br/>

                                                @endforeach
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
            <a href="{{route("line_product_station.machine_type.output_band.index",$machine_type)}}"
               class="btn btn-outline-dark">بازگشت</a>
        </div>
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
                "line_output_number": "required",
            }
        });
        $('#form2').validate({
            rules: {
                "goods_kind_id_auto": "required",
            }
        });
    </script>
@endsection
