@extends('layouts.admin._master')

@section('page_header_title',"داشبورد مدیریت پیمانکاران ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دستور پیمان {{$production->serial()}}</h5>
                </div>
                <div class="card-block">

                    @include("contractor.public._info_small")

                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5> انتخاب پیمانکار</h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("contractor.admin.contractor_allocation.submit",$production)}}"
                          method="post" novalidate="novalidate">
                        @csrf

                        <div class="row">

                            @if(count($contractor_option)> 1)
                                @include("component.input._select",["id"=>"line_product_station_id","label"=>"پیمانکار","option"=>$contractor_option])
                            @else
                                @include("component.input._lable",["id"=>"line_product_station_id","label"=>"پیمانکار","value"=>$contractor_option[0]["text"]])
                                @include("component.input._hidden",["id"=>"line_product_station_id","label"=>"پیمانکار","value"=>$contractor_option[0]["value"]])
                            @endif


                            <br/>
                            <div class="table-responsive">
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th colspan="{{5+($production->product->sub_unit?1:0)}}"></th>
                                        <th colspan="3">
                                            موجودی
                                            ({{$production->product->unit->caption}})
                                        </th>
                                        <th>

                                            <a href="{{route("contractor.admin.contractor_allocation_quick.select_packing_forms",[$contractor_id,$production_channel_type])}}"
                                               class="btn btn-outline-primary">انتخاب بسته بندی ها </a>

                                        </th>
                                    </tr>
                                    <tr>
                                        <th>ردیف</th>
                                        <th><input type="checkbox" id="check_all"></th>

                                        <th>کارت پیمان</th>
                                        <th>نام کالا</th>
                                        <th>مقدار تخصیص
                                            ({{$production->product->unit->caption}})

                                        </th>
                                        @if($production->product->sub_unit)
                                            <th>

                                                واحد فرعی
                                                ({{$production->product->sub_unit->caption??""}})

                                            </th>
                                        @endif
                                        <th>کل</th>
                                        <th>کارت جاری</th>
                                        <th>مجاز</th>
                                        <th>نوع بسته بندی مجاز</th>
                                        {{--                                        <th>تخصیص مجدد</th>--}}
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            <input type="checkbox" checked="checked" disabled>
                                            <input type="hidden" name="">
                                        </td>

                                        <td>{{$production->serial()}}</td>
                                        <td>{{$production->product->caption}}</td>
                                        <td>
                                            {{--                                        مقدار تخصیص با توجه به واحد بچ کالا (اگر بچی باشد) مقدار دهی شده است.--}}

                                            <input name="production_allocation_amount"
                                                   id="production_allocation_amount"
                                                   class="amount_number"
                                                   type="number"
                                                   min="0" max="{{$production->number}}"
                                                   value="{{isset($production_selected_by_packing_forms_list[$production->id])?$production_selected_by_packing_forms_list[$production->id]: $production->number}}">


                                        </td>
                                        @if($production->product->sub_unit)
                                            <td>
                                                <input
                                                        id="sub_unit_{{$production->id}}"
                                                        disabled
                                                        type="number"
                                                        min="0" max="{{$production->number}}"
                                                        value="{{round($production->number*$production->product->weight,2)}}">

                                            </td>
                                        @endif
                                        <td>
                                            @if(isset($product_bom_inventory[$production->product_id]))
                                                <a href="{{route("contractor.admin.contractor_allocation.show_product_inventory",[$production,$production->id,$production->product_id,$contractor_id,$production_channel_type])}}">
                                                    {{$product_bom_inventory[$production->product_id]}}
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{$production_packing_forms_list[$production->id]}}</td>
                                        <td></td>
                                        <td>
                                            @foreach($production->packing_types as $production_packing_type)
                                                {{$production_packing_type->packing_type->caption}}<br/>
                                            @endforeach
                                        </td>
                                        <td class="text-danger">

                                        </td>
                                    </tr>


                                    {{--            تخصیص دیگر کارت هایی که می توانند با این کارت همراه شوند.--}}
                                    @foreach($production_allocation_togethers  as $together_key =>$together_item)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>
                                                @if( isset($production_selected_by_packing_forms_list[$together_key]))
                                                    <input
                                                            data-key="{{$together_key}}"
                                                            class="together_checkbox" type="checkbox"
                                                            checked disabled
                                                    >
                                                    <input name="together_checkbox[{{$together_key}}]"
                                                           type="hidden" value="1"

                                                    >

                                                @else
                                                    <input name="together_checkbox[{{$together_key}}]"
                                                           data-key="{{$together_key}}"
                                                           class="together_checkbox" type="checkbox"

                                                    >
                                                @endif


                                            </td>

                                            <td>{{$together_item["production"]->serial()}}</td>
                                            <td>{{$together_item["production"]->product->caption}}</td>
                                            <td>
                                                {{--                                        مقدار تخصیص با توجه به واحد بچ کالا (اگر بچی باشد) مقدار دهی شده است.--}}
                                                @if($together_item["machine_allocation"])
                                                    {{--                                            تخصیص مجدد ادامه تخصیص--}}
                                                    {{--                                                    <input type="hidden"--}}
                                                    {{--                                                           name="together_allocation_amount[{{$together_key}}]"--}}
                                                    {{--                                                           id="together_allocation_amount_{{$together_key}}" min="0"--}}
                                                    {{--                                                           max="{{$together_item["allocation_amount"]}}"--}}
                                                    {{--                                                           value="{{$together_item["allocation_amount"]}}">--}}
                                                    {{--                                                    {{$together_item["allocation_amount"]}}--}}
                                                @else
                                                    <input name="together_allocation_amount[{{$together_key}}]"
                                                           id="together_allocation_amount_{{$together_key}}"
                                                           type="number"
                                                           data-id="{{$together_key}}"
                                                           data-weight="{{$together_item["production"]->product->weight}}"
                                                           class="amount_number"
                                                           min="0" max="{{$together_item["allocation_amount"]}}"
                                                           value="{{isset($production_selected_by_packing_forms_list[$together_key])?$production_selected_by_packing_forms_list[$together_key]:$together_item["allocation_amount"]}}"
                                                            {{isset($production_selected_by_packing_forms_list[$together_key])?"":"disabled"}}

                                                    >
                                                @endif

                                            </td>
                                            @if($production->product->sub_unit)
                                                <td>
                                                    <input
                                                            id="sub_unit_{{$together_key}}"
                                                            disabled
                                                            type="number"
                                                            min="0" max="{{$production->number}}"
                                                            value="{{round($together_item["allocation_amount"]*$together_item["production"]->product->weight,2)}}">
                                                </td>
                                            @endif
                                            <td>
                                                @if(isset($product_bom_inventory[$together_item["production"]->product_id]))
                                                    <a href="{{route("contractor.admin.contractor_allocation.show_product_inventory",[$production,$together_item["production"]->id,$together_item["production"]->product_id,$contractor_id,$production_channel_type])}}">
                                                        {{$product_bom_inventory[$together_item["production"]->product_id]}}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{$production_packing_forms_list[$together_item["production"]->id]}}</td>

                                            <td></td>
                                            <td>
                                                @foreach($together_item["production"]->packing_types as $production_packing_type)
                                                    {{$production_packing_type->packing_type->caption}}<br/>
                                                @endforeach
                                            </td>
                                            <td class="text-danger">
                                                @if($together_item["machine_allocation"])
                                                    تخصیص مجدد ادامه تخصیص
                                                    {{$together_item["machine_allocation"]->parent_allocation_id}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td></td>
                                        <td></td>

                                        <td></td>
                                        <td>جمع کل تخصیص</td>
                                        <th>
                                            <span id="sum_allocation_selected">0</span>
                                            {{$production->product->unit->caption}}
                                        </th>
                                        @if($production->product->sub_unit)
                                            <th>
                                                <span id="sum_sub_allocation_selected"></span>
                                                {{$production->product->sub_unit->caption}}
                                            </th>
                                        @endif
                                        <td></td>

                                    </tr>
                                    </tbody>

                                </table>
                            </div>


                            <div class="col-md-12">
                                <a href="{{route("contractor.admin.dashboard.view_card",$production)}}"
                                   class="btn btn-outline-dark">بارگشت </a>
                                <button type="submit" class="btn btn-primary">ثبت تخصیص</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        @include("production.public.log_status")

    </div>

@endsection
@section("styles")
    <style>
        .amount_number {
            width: 80px !important;
        }
    </style>
@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                allocation_amount: {required: true, min: 0, max: {{$allocation_amount}}},
                line_product_station_id: {required: true},
            }
        });
        $(".together_checkbox").click(function () {

            var disabled = 'disabled';
            if ($(this).is(":checked")) {
                disabled = null
            }

            $("#together_allocation_amount_" + $(this).data('key')).attr('disabled', disabled);
            update_sum();
        });
        $("#check_all").click(function () {


            $(".together_checkbox").click();

        })
        $(".amount_number").change(function () {
            update_sum();
        })

        $("#production_allocation_amount").change(function () {
            weight = Math.round(parseFloat($(this).val()) * parseFloat("{{$production->product->weight}}") * 100) / 100;
            $("#sub_unit_" + "{{$production->id}}").val(weight);
        })

        function update_sum() {
            let total = parseFloat($("#production_allocation_amount").val()) + 0;
            let sum_weight = parseFloat($("#sub_unit_" + "{{$production->id}}").val()) + 0;
            $('input[name^="together_allocation_amount["]').each(function () {
                if (!$(this).is(':disabled')) {
                    let val = parseFloat($(this).val()) || 0;
                    total += val;
                    weight = Math.round(parseFloat(val) * parseFloat($(this).data("weight")) * 100) / 100;
                    $("#sub_unit_" + ($(this).data("id"))).val(weight);
                    sum_weight += weight;
                }
            });
            sum_weight = Math.round(sum_weight * 100) / 100;
            total = Math.round(total * 100) / 100;
            $("#sum_allocation_selected").html(total);
            @if($production->product->sub_unit)
            $("#sum_sub_allocation_selected").html(sum_weight);
            @endif
        }

        update_sum();
        $(".amount_number").change();
    </script>

@endsection
