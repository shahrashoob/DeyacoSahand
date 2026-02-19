@extends('layouts.admin._master')

@section('page_header_title'," داشبورد جاری تولید - ".$production->product->goods_kind->caption)

@section('content')
    <form id="form1"
          action="{{route("fabric.finishing_machine.machine_allocation.submit",[$machine,$production])}}"
          method="post"
          novalidate="novalidate">
        @csrf

        <div class="row">

            <div class="w-25"></div>
            <div class="col-sm-12 col-md-12 col-md-offset-3">

                <div class="card">
                    <div class="card-header">
                        <h5>تخصیص کالا به {{$machine->caption}}  </h5>

                    </div>
                    @php $replace_number=0; @endphp

                    @include("component.input._hidden",["id"=>"first_line_product_station_id","value"=>$first_line_product_station->id])
                    @include("component.input._hidden",["id"=>"current_machine_allocation_id","value"=>$current_machine_allocation_id??0])
                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th><input type="checkbox" id="check_all"></th>

                                <th>کارت تولید</th>
                                <th>نام کالا</th>
                                <th>مقدار تخصیص

                                    @switch($first_line_product_station->material_unit_type_id_dependent_to_batch)
                                        @case(1)
                                            ({{$production->product->unit->caption}})
                                            @break

                                        @case(2)
                                            ({{$production->product->sub_unit->caption??"***"}})
                                            @break

                                        @case(4)
                                            (تعداد بسته بندی)
                                            @break

                                    @endswitch

                                </th>
                                <th>نوع بسته بندی مجاز</th>
                                <th>تخصیص مجدد</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @if(!$current_machine_allocation_id)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <input type="checkbox" checked="checked" disabled>
                                        <input type="hidden" name="">
                                    </td>

                                    <td>{{$production->serial()}}</td>
                                    <td>{{$production->product->caption}}</td>
                                    <td>

                                        <input name="main_allocation_amount" type="number" min="0"
                                               max="{{$allocation_amount_list[$first_line_product_station->material_unit_type_id_dependent_to_batch??1]}}"
                                               value="{{$allocation_amount_list[$first_line_product_station->material_unit_type_id_dependent_to_batch??1]}}"
                                               class="amount_number" style="width: 60px">
                                    </td>
                                    <td>
                                        @foreach($production->packing_types as $production_packing_type)
                                            {{$production_packing_type->packing_type->caption}}<br/>
                                        @endforeach
                                    </td>
                                    <td>---</td>
                                </tr>
                            @endif

                            {{--            تخصیص دیگر کارت هایی که می توانند با این کارت همراه شوند.--}}
                            @foreach($production_allocation_togethers  as $together_key =>$together_item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <input name="together_checkbox[{{$together_key}}]" data-key="{{$together_key}}"
                                               class="together_checkbox" type="checkbox"
                                               @if($together_item["machine_allocation"] && $together_item["machine_allocation"]->id == $current_machine_allocation_id )
                                                   checked
                                                @endif
                                        >
                                    </td>

                                    <td>{{$together_item["production"]->serial()}}</td>
                                    <td>{{$together_item["production"]->product->caption}}</td>
                                    <td>
                                        {{--                                        مقدار تخصیص با توجه به واحد بچ کالا (اگر بچی باشد) مقدار دهی شده است.--}}
                                        @if($together_item["machine_allocation"])
                                            {{--                                            تخصیص مجدد ادامه تخصیص--}}

                                            <input name="together_allocation_amount[{{$together_key}}]"
                                                   id="together_allocation_amount_{{$together_key}}" type="number"
                                                   min="0" max="{{$together_item["allocation_amount"]}}"
                                                   value="{{$together_item["allocation_amount"]}}"
                                                   @if($together_item["machine_allocation"] && $together_item["machine_allocation"]->id == $current_machine_allocation_id )

                                                   @else
                                                       disabled
                                                    @endif
                                            >
                                        @else
                                            <input name="together_allocation_amount[{{$together_key}}]"
                                                   id="together_allocation_amount_{{$together_key}}" type="number"
                                                   min="0" max="{{$together_item["allocation_amount"]}}"
                                                   value="{{$together_item["allocation_amount"]}}" disabled>
                                        @endif

                                    </td>
                                    <td>
                                        @foreach($together_item["production"]->packing_types as $production_packing_type)
                                            {{$production_packing_type->packing_type->caption}}<br/>
                                        @endforeach
                                    </td>
                                    <td class="text-danger">
                                        @php $has_parent=0; @endphp
                                        @if($together_item["machine_allocation"])
                                            @php $has_parent=1; @endphp
                                            تخصیص مجدد ادامه تخصیص
                                            {{$together_item["machine_allocation"]->parent_allocation_id}}
                                        @endif
                                        @if($has_parent==0)
                                            ---
                                        @endif
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>

                        </table>
                    </div>


                </div>
            </div>


            <div class="w-25"></div>
            {{--            @if(count($reserve_after_allocation_option)>0)--}}
            {{--                <div class="col-sm-12 col-md-6 col-md-offset-3">--}}
            {{--                    <div class="card">--}}
            {{--                        <div class="card-block border-bottom">--}}
            {{--                            <div class="row d-flex align-items-center">--}}
            {{--                                <div class="col-auto">--}}


            {{--                                    <i id="i_{{$i}}"--}}
            {{--                                       class="fa fa-vial  fa-2x text-success"></i>--}}

            {{--                                </div>--}}
            {{--                                <div class="col">--}}

            {{--                                    @include("component.input._select",["id"=>"reserve_after_allocation_id","label"=>" کارت تولید نمونه گیری--}}
            {{--                                    بعد از کارت زیر  بر روی ماشین رزور شود.","option"=>$reserve_after_allocation_option,"class_col"=>"col-md-12"])--}}

            {{--                                </div>--}}

            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            @endif--}}

            <div class="col-sm-12" style="text-align: center">

                <button type="submit" class="btn btn-primary" id="btn_replace">ثبت تخصیص و ادامه</button>
                <a href="{{route("fabric.production_card.view_card",$production)}}" class="btn btn-outline-dark">بازگشت
                    به کارتابل تولید</a>
            </div>


        </div>
    </form>
@endsection

@section("style")
    <style>
        .amount_number {
            width: 60px !important;
        }
    </style>
@endsection
@section("scripts")
    <script>
        var count_check = "{{$band_count_allocation}}";
        $(".together_checkbox").click(function () {

            var disabled = 'disabled';
            if ($(this).is(":checked")) {
                disabled = null
            }

            $("#together_allocation_amount_" + $(this).data('key')).attr('disabled', disabled);
        });
        $("#check_all").click(function () {


            $(".together_checkbox").click();

        })

    </script>
@endsection


