@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت
                        <b>
                            ورودی ها
                        </b>
                        از گروه ماشین
                        <b>
                            {{$machine_type->caption}}
                        </b>
                        <a class="btn btn-success"
                           href="{{route("line_product_station.machine_type.input_band.create",$machine_type)}}"><i
                                class="fa fa-plus-circle"></i> افزودن ورودی جدید </a>
                    </h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد</th>
                                <th>نام ورودی</th>
                                <th> رسته کالایی مجاز (تاثیر اشتراکی بر همبافت)

                                </th>

                                <th> بسته بندی های مجاز</th>
                                <th>تعداد خط ورودی</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($machine_type->input_bands as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->code}}

                                    </td>
                                    <td>
                                        <a href="{{ $item->machine_type->active_status_id == 1200?route("line_product_station.machine_type.input_band.edit",[$machine_type,$item]):""}}">{{$item->caption}}</a>

                                    </td>
                                    <td>
                                        @foreach($item->goods_kinds as $goods_kind_item)
                                            {{$goods_kind_item->goods_kind->caption}}
                                            ({{$goods_kind_item->effect_is_shared?"دارد":"ندارد"}}),

                                        @endforeach
                                        <a class="text-success"
                                           href="{{route("line_product_station.machine_type.input_band.add_goods_kind",$item)}}"><i
                                                class="fa fa-plus-circle"></i> </a>
                                    </td>
                                    <td>

                                        @foreach($item->goods_kinds as $goods_kind_item)

                                            {{count($goods_kind_item->packing_type_list())}} بسته بندی مجاز برای

                                            {{$goods_kind_item->goods_kind->caption}}

                                            <br/>

                                        @endforeach

                                    </td>

                                    <td>
                                        {{$item->input_line_number}}
                                    </td>
                                    <td>
                                        {{($item->active_status->caption??"")}}

                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </div>


        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>مدیریت
                        <b>
                            الگوریتم های درخواست مواد اولیه
                        </b>
                        برای گروه ماشین
                        <b>
                            {{$machine_type->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.machine_type.input_band.update_goods_kind_algorithm",$machine_type)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> رسته کالایی</th>
                                <th>آیا درخواست ها به صورت مستقل باشد<br/> یا همراه با دیگر رسته کالایی ها</th>
                                <th>نوع الگوریتم برای کالا های تولیدی</th>
                                <th>نوع الگوریتم برای کارت های نمونه گیری</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($machine_type->getGoodsKind() as $goods_kind_item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td><br/>{{$goods_kind_item->caption}}</td>
                                    <td>
                                        @include("component.input._radio_box01",["id"=>"dependency_to_other_goods_kind_".$goods_kind_item->id,"value"=>$dependency_to_other_goods_kind[$goods_kind_item->id],"label"=>"","value1"=>1,"label1"=>"مستقل","value0"=>0,"label0"=>"وابسته به رسته کالایی های دیگر"])

                                    </td>
                                    <td>
                                        @include("component.input._select",[
                                    "id"=>"algorithm_type_id_".$goods_kind_item->id,
                                    "label"=>"",
                                    "option"=>$raw_material_request_algorithm_type_option[$goods_kind_item->id]["items"],
                                    "val"=>$raw_material_request_algorithm_type_option[$goods_kind_item->id]["value"],
                                    "text"=>$raw_material_request_algorithm_type_option[$goods_kind_item->id]["text"],
                                    "class_col"=>""
                                    ])

                                    </td>

                                    <td>
                                        @include("component.input._select",[
                                    "id"=>"sampling_algorithm_type_id_".$goods_kind_item->id,
                                    "label"=>"",
                                    "option"=>$raw_material_request_sampling_algorithm_type_option[$goods_kind_item->id]["items"],
                                    "val"=>$raw_material_request_sampling_algorithm_type_option[$goods_kind_item->id]["value"],
                                    "text"=>$raw_material_request_sampling_algorithm_type_option[$goods_kind_item->id]["text"],
                                    "class_col"=>""
                                    ])

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                        <button type="submit" class="btn btn-primary"> ذخیره اطلاعات</button>
                    </form>
                </div>

            </div>
            <div>
                <a href="{{route("line_product_station.machine_type.index",$machine_type->station_id)}}"
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
                @foreach($machine_type->getGoodsKind() as $goods_kind_item)
                algorithm_type_id_{{$goods_kind_item->id}}: "required",
                sampling_algorithm_type_id_{{$goods_kind_item->id}}: "required",
                @endforeach
            }
        });
    </script>
@endsection

