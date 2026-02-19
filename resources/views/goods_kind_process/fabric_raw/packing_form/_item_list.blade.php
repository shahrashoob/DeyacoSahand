@if(count($packing_form->items)>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5>لیست آیتم های موجود در بسته بندی</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive center">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>ردیف</th>
                            @if($packing_form->items()->first()->product->supply_type_id ==2)
                                <th>شماره فرم خرید</th>
                                <th>سریال کارت خرید</th>
                                <th>وضعیت سریال خرید</th>
                            @else

                                <th>شماره فرم تولید</th>
                                <th>سریال تولید</th>
                                <th>وضعیت سریال تولید</th>
                            @endif
                            <th>سریال سطح بالا</th>
                            <th>وضعیت سریال سطح بالا</th>
                            <th>کد کالا</th>
                            <th>نام کالا</th>
                            <th>درجه کالا</th>
                            <th>لات</th>
                            <th>{{$packing_form->getUnitCaption("unit","measurement")}}</th>
                            <th>{{$packing_form->getUnitCaption("sub_unit","measurement")}}</th>
                            <th>{{$packing_form->getUnitCaption("sub_unit2","measurement")}}</th>

                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($packing_form->items as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>{{$item->getCode(false,false,$show_packing_code??1)}}</td>
                                <td>{{$item->production_form_item->code??""}}</td>
                                <td>{{$item->production_form_item->production->serial??""}}</td>
                                <td>{{$item->production_form_item->production->waiting_status->caption??""}}</td>
                                <td>
                                    @if($item->production_form_item && $item->production_form_item->production &&  $item->production_form_item->production->parent_production)
                                    <a href="{{route("contractor.admin.dashboard.view_card",$item->production_form_item->production->parent_production )}}" >
                                        {{$item->production_form_item->production->parent_production->serial??""}}
                                    </a>
                                    @endif
                                </td>
                                <td>{{$item->production_form_item->production->parent_production->waiting_status->caption??""}}</td>
                                <td>{{$item->product->code}}</td>
                                <td>{{$item->product->caption}}
                                    @if(isset($item->version_code))
                                       (V{{$item->version_code}})
                                    @endif
                                </td>
                                <td>{{$item->degree->caption}}</td>
                                <td>{{$item->lot_number->code}}</td>
                                <td>{{round($item->final_amount,6)}}</td>
                                <td>
                                    @if($item->product->sub_unit)
                                        {{round($item->sub_amount,6)}}
                                    @endif
                                </td>
                                <td>
                                    @if($item->product->sub_unit2 && $item->product->sub_unit2_id==1400 && $item->sub_amount2  )

                                        @php $x=explode('.', (string)$item->sub_amount2)[1] ?? '';  @endphp
                                        {{$x!=''? "".$x.". + ":""}}

                                        {{floor($item->sub_amount2)}}
                                        <br/>
                                        ({{$item->product->frame_ratio_unit2 * (1-($final_shrinkage_percent/100))}})

                                    @elseif($item->product->sub_unit2 && $item->product->sub_unit2_id!=1400 && $item->sub_amount2 )

                                        {{round($item->sub_amount2,6)}}
                                    @endif
                                </td>
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
@endif

