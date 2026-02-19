@extends('layouts.admin._master')
@section("page_header_title","مدیریت اطلاعات  ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> جزئیات محاسبات موجودی انبار در تخصیص شماره
                        {{$allocation_id??""}}
                    </h5>
                </div>
                <div class="card-block">
                    @php $row=1;@endphp
                    @if(isset($json_data->end_result))
                        @foreach($json_data->end_result as $end_result_item)
                            <div class="table-responsive">
                                <table class="table table-styling">
                                    <tbody>
                                    <tr>

                                        <th style="text-align: center">
                                            حداکثر مقدار قابل تخصیص
                                            @if(isset($end_result_item->bom_permutation))
                                                کالای جایگزین {{$end_result_item->bom_permutation->getSystemCode()}}
                                            @else
                                                کالای اصلی
                                            @endif
                                            برابر با
                                            <b>{{$end_result_item->amount_can_be_produced}}</b>
                                            {{$production->product->unit->caption??""}}
                                            می باشد.
                                            @if(isset($end_result_item->not_allowed_for_production) && $end_result_item->not_allowed_for_production)
                                                <div class="text-danger">این کالا جزء کالاهای مجاز تولید نمی باشد</div>
                                            @endif
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>


                                            <table class="tbl_info">
                                                <tr>
                                                    <td>کد کالای مصرفی</td>
                                                    <td>خط ورودی</td>
                                                    <td>اولویت</td>
                                                    <td>مقدار مورد نیاز
                                                        <br/>
                                                        (برای یک واحد کالا)
                                                    </td>
                                                    {{--                                                    <td>مقدار مورد نیاز--}}
                                                    {{--                                                        <br/>--}}
                                                    {{--                                                        (برای کل کارت تولید)--}}
                                                    {{--                                                    </td>--}}
                                                    <td>موجودی انبار</td>
                                                    <td>مقدار مورد نیاز برای کارت های جاری و رزرو</td>
                                                    <td>مقدار مصرف شده</td>
                                                    <td>
                                                        موجودی فعال
                                                        <br/>
                                                        موجودی انبار - (مقدار مورد نیاز کارت های جاری و رزرو - مقدار
                                                        مصرف
                                                        شده)
                                                    </td>
                                                    <td>
                                                        مقداری از کالا که می توان با موجودی فعال تولید کرد
                                                    </td>

                                                </tr>
                                                @foreach($end_result_item->material as $material_info)
                                                    @php
                                                        $product_title=isset($all_material[$material_info->material_id])?$all_material[$material_info->material_id]:$material_info->product_code;
                                                    @endphp
                                                    <tr>
                                                        <td>

                                                            <a href="#!"
                                                               title="{{$product_title}}">{{$material_info->product_code}}</a>
                                                        </td>
                                                        <td>{{$material_info->input_line_code}}</td>
                                                        <td>{{$material_info->priority_number}}</td>
                                                        <td>{{isset($material_info->amount_required)?round($material_info->amount_required,6):"***"}}</td>
                                                        {{--                                                        <td>{{isset($material_info->material_amount_for_production)?round($material_info->material_amount_for_production,2):"***"}}</td>--}}
                                                        <td>
                                                            <div class="btn-group mb-2 mr-2 show">
                                                                <a class="   dropdown-toggle" type="button"
                                                                   data-toggle="dropdown"
                                                                   aria-haspopup="true" aria-expanded="true">
                                                                    {{isset($material_info->inventory)?round($material_info->inventory,6):"***"}}
                                                                </a>
                                                                <div class="dropdown-menu " x-placement="bottom-start"
                                                                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 45px, 0px);">
                                                                    <div class="center">موجودی {{$product_title}}</div>
                                                                    <a class="dropdown-item center" href="#!">
                                                                        <div
                                                                            style="max-height: 200px; overflow: auto; padding: 15px">
                                                                            <table>
                                                                                <tr>
                                                                                    <td>انبار</td>
                                                                                    <td>در زمان تخصیص</td>
                                                                                    <td>اکنون</td>

                                                                                </tr>
                                                                                @php
                                                                                    $sum_before_value=0;
                                                                                    $sum_current_value=0;
                                                                                @endphp
                                                                                @foreach($warehouse_list as $warehouse_id=>$warehouse_caption)

                                                                                    @if(
                                                                                        isset($before_inventory[$material_info->material_id."_".$warehouse_id])
                                                                                        ||
                                                                                        isset($current_inventory[$material_info->material_id."_".$warehouse_id])
                                                                                        )
                                                                                        @php
                                                                                            $current_value=isset($current_inventory[$material_info->material_id."_".$warehouse_id])?$current_inventory[$material_info->material_id."_".$warehouse_id]->value:0;

                                                                                            $before_value=isset($before_inventory[$material_info->material_id."_".$warehouse_id])?$before_inventory[$material_info->material_id."_".$warehouse_id]->value:0;
                                                                                            if($current_value==0 && $before_value==0){
                                                                                                continue;
                                                                                            }
                                                                                            $sum_before_value+=$before_value;
                                                                                            $sum_current_value+=$current_value;
                                                                                        @endphp
                                                                                        <tr>
                                                                                            <td> {{$warehouse_caption}}</td>
                                                                                            <td> {{$before_value }}</td>
                                                                                            <td> {{ $current_value }}</td>

                                                                                        </tr>
                                                                                    @endif

                                                                                @endforeach
                                                                                <tr>
                                                                                    <td>جمع کل</td>
                                                                                    <td>{{$sum_before_value}}</td>
                                                                                    <td>{{$sum_current_value}}</td>

                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </a>

                                                                </div>
                                                            </div>


                                                        </td>
                                                        <td>{{isset($material_info->amount_required_reserve)?round($material_info->amount_required_reserve,6):"***"}}</td>
                                                        <td>{{isset($material_info->consumed_amount)?round($material_info->consumed_amount,6):"***"}}</td>

                                                        <td>{{isset($material_info->active_inventory)?round($material_info->active_inventory,6):"***"}}</td>
                                                        <td>{{isset($material_info->amount_for_production)?round($material_info->amount_for_production,6):"***"}}</td>


                                                    </tr>
                                                @endforeach
                                            </table>


                                        </td>
                                    </tr>


                                    </tbody>

                                </table>
                            </div>
                        @endforeach
                    @endif
                    <a href="{{ url()->previous() }}" class="btn btn-outline-dark"> بازگشت</a>
                </div>

            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست کارت های جاری و رزرو در زمان تخصیص شماره
                        {{$allocation_id??""}}
                    </h5>
                </div>
                <div class="card-block">
                    @php $row=1;@endphp
                    @if(isset($json_data->current_reserve_allocation_list))
                        <div class="table-responsive">

                            <table class="tbl_info">
                                <tr>
                                    <td>کد کالای مصرفی
                                    </td>
                                    <td>ماشین</td>
                                    <td>شماره تخصیص</td>
                                    <td>کارت تولید</td>
                                    <td>مقدار مورد نیاز</td>


                                </tr>
                                @foreach($json_data->current_reserve_allocation_list as $current_reserve_allocation_item)
                                    <tr>
                                        <td>{{isset($all_material[$current_reserve_allocation_item->material_id])?$all_material[$current_reserve_allocation_item->material_id]:"product id ".$current_reserve_allocation_item->material_id}}</td>


                                        @if(isset($all_allocation[$current_reserve_allocation_item->allocation_id]))
                                            <td>
                                                {{$all_allocation[$current_reserve_allocation_item->allocation_id]->machine->caption??""}}
                                            </td>
                                            <td>
                                                {{$current_reserve_allocation_item->allocation_id}}
                                            </td>

                                            <td>
                                                @foreach($all_allocation[$current_reserve_allocation_item->allocation_id]->items as $machine_allocation)
                                                    {{$machine_allocation->production->serial}}
                                                    @break
                                                @endforeach
                                            </td>
                                        @else
                                            <td></td>
                                            <td>
                                                {{$current_reserve_allocation_item->allocation_id}}
                                            </td>
                                            <td></td>
                                        @endif


                                        <td>{{$current_reserve_allocation_item->amount_required}}</td>
                                    </tr>
                                @endforeach
                            </table>

                        </div>
                    @endif
                    <a href="{{ url()->previous() }}" class="btn btn-outline-dark"> بازگشت</a>
                </div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <style>
        .tbl_info td {
            padding: 2px;
            text-align: center;
            border: 1px solid;
        }

        .tbl_info {
            width: 100%;
        }
    </style>
@endsection


