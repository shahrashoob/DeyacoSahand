@include("component.formatDecimal9")
@switch($product->supply_type_id )
    @case(1)
        <div class="table-responsive center" style="font-size: 12px">
            <table class="table table-styling">
                <thead>
                <tr>
                    <th>#</th>
                    <th>ماده اولیه</th>
                    <th>
                        وابسته به
                        <br/>
                        (واحد مرجع کالای اصلی / ماده اولیه)
                    </th>
                    <th>ایستگاه کاری</th>
                    <th>عملیات در ایستگاه کاری</th>
                    <th>عملیات فرعی</th>
                    <th>انبار تحویل<br/> کالا</th>
                    <th> انبار مصرف <br/>کالای تولیدی</th>
                    <th>انبار مصرف <br/> نمونه گیری</th>
                    <th>مقدار</th>
                    <th>تعداد</th>
                    <th>درصد استفاده</th>
                    <th>خط ورودی</th>
                    <th>روش تزریق</th>
                    <th>درجه بندی</th>
                    <th>جایگزین مصرف</th>
                    <th>درصد پیش بینی ضایعات</th>
                    <th>ضریب اصلاح مصرف</th>
                    <th>پیش بینی ضریب اصلاح مصرف</th>
                    <th>نقص های غیر مجاز</th>
                    <th>ضریب مصرف کانال تولید</th>
                    <th></th>
                </tr>

                </thead>
                <tbody>
                @php $list=$bom->items()->join("products","products.id","material_id")->select("bill_of_material_item.*")->orderBy("products.goods_kind_id")->paginate(20);@endphp
                @php $row=$list->firstItem(); @endphp
                @foreach($list as $item)
                    <tr
                            @if($item->degrees()->count()==0 ) style="background: #efc79a" @endif
                    >
                        <td>{{$row++}}</td>
                        <td>
                            @if($item->is_structure_product)
                                <span class="text-primary">
                                    <i class="fa fa-medal"></i>
                                    {{($item->material->code??"")." - ".($item->material->caption??"")}}
                                    <br/>
                              کالای ساختاری  </span>
                            @else
                                {{($item->material->code??"")." - ".($item->material->caption??"")}}
                            @endif
                        </td>
                        <td>
                           @if($item->bill_of_material_dependency_type_id == 1)
                                {{$item->dependent_on_material?($item->dependent_on_material->code):"کالای اصلی"}}
                                (
                                {!! $item->get_dependent_on_main_unit_type() !!}
                                / {!! $item->get_dependent_on_material_unit_type() !!}
                                )
                            @else
                                    وابسته به مسیر محصول
                            @endif
                        </td>
                        <td>{{$item->station->caption??""}}</td>
                        <td>{{$item->station_operation->caption??""}}</td>
                        <td>{{$item->station_sub_operation->caption??""}}</td>
                        <td>{{in_array($item->warehouse_type_id,[100])?$item->warehouse_type->caption:$item->warehouse->caption}}</td>

                        <td>{{in_array($item->productive_consume_warehouse_type_id,[2,100])?$item->productive_consume_warehouse_type->caption:$item->productive_consume_warehouse->caption}}</td>
                        <td>{{in_array($item->sampling_consume_warehouse_type_id,[2,100])?$item->sampling_consume_warehouse_type->caption:$item->sampling_consume_warehouse->caption}}</td>

                        <td style="text-align: left">{{formatDecimal9($item->amount??"")}}</td>
                        <td>{{$item->number}}</td>
                        <td>{{$item->percent_of_use}}</td>
                        <td>{{$item->input_line_code}}</td>
                        <td>{{$item->bill_of_material_entering_type->caption}}</td>
                        <td>
                            <a href="{{route("line_product_station.product.bom_degree.index",[$product,$item->material_id,$item,$product_creation_process])}}">
                                {{$item->degrees()->count()}} درجه
                            </a>
                        </td>
                        <td>
                            @if($item->material->replace_product()->count()==0)
                                ندارد

                            @else
                                <a href="{{route("line_product_station.product.bom_replace.index",[$product,$item->material_id,$item,$product_creation_process])}}">

                                    @if($item->replaces()->count() ==0)
                                        انتخاب جایگزین
                                    @else
                                        {{$item->replaces()->count()}}
                                        جایگزین
                                    @endif
                                </a>
                            @endif
                        </td>
                        <td>{{$item->waste_prediction}} %</td>
                        <td>{{$item->consumption_correction_factor}}</td>
                        <td>{{$item->consumption_correction_factor_prediction}}</td>
                        <td>
                            @if($item->has_material_goods_kind_fault())
                                <a href="{{route("line_product_station.product.bom_fault_illegal.index",[$product,$item->material_id,$item,$product_creation_process])}}">

                                    @php $bom_fault_illegals=$item->bom_fault_illegals()->count();@endphp
                                    @if($bom_fault_illegals==0)
                                        انتخاب نقص
                                    @else
                                        {{$bom_fault_illegals}}
                                        نقص غیرمجاز
                                    @endif
                                </a>
                            @else
                                ندارد
                            @endif
                        </td>
                        <td>{{$item->consumption_percent_of_production_channel}} </td>
                        <td>
                            <a href="{{route("line_product_station.product.bom_item.destroy",$item)}}"
                               onclick="return confirm('آیا از حذف اطمینان دارید؟')" class="text-danger"><i
                                        class="fa fa-trash"></i> </a>

                            <a href="{{route("line_product_station.product.bom_item.edit",[$item,$product_creation_process??null])}}"
                                ><i
                                        class="fa fa-edit"></i> </a>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>
        <div class="float-left">
            نمايش رکوردهای
            <b>{{$list->firstItem()}}</b>
            تا
            <b>{{$list->lastItem()}}</b>
            از
            <b>{{$list->total()}}</b>
            رکورد موجود
        </div>

        <div class="text-center">
            {{$list->links('pagination::bootstrap-4')}}
        </div>
        @break

    @case(3)
        <div class="table-responsive center" style="font-size: 12px">
            <table class="table table-styling">
                <thead>
                <tr>
                    <th>#</th>
                    <th>ماده اولیه</th>
                    <th>
                        وابسته به
                        <br/>
                        (واحد مرجع کالای اصلی / ماده اولیه)
                    </th>
                    <th>پیمانکار</th>
                    <th>عملیات پیمانکار</th>
                    <th>انبار تحویل<br/> کالا</th>
                    <th> انبار مصرف <br/>کالای تولیدی</th>
                    <th>انبار مصرف <br/> نمونه گیری</th>
                    <th>واحد تحویل کالا</th>
                    <th>مقدار</th>
                    <th>تعداد</th>
                    <th>درصد استفاده</th>
                    <th>درجه بندی</th>
                    <th>درصد پیش بینی ضایعات</th>
                    <th>ضریب اصلاح مصرف</th>
                    <th>پیش بینی ضریب اصلاح مصرف</th>
                    <th>نقص های غیر مجاز</th>
                    <th></th>
                </tr>

                </thead>
                <tbody>
                @php $row=1; @endphp
                @foreach($bom->items as $item)
                    <tr @if($item->degrees()->count()==0 ) style="background: #efc79a" @endif>
                        <td>{{$row++}}</td>
                        <td>{{($item->material->code??"")." - ".($item->material->caption??"")}}</td>
                        <td>
                            {{$item->dependent_on_material?($item->dependent_on_material->code):"کالای اصلی"}}
                            (
                            {!! $item->get_dependent_on_main_unit_type() !!}
                            / {!! $item->get_dependent_on_material_unit_type() !!}
                            )
                        </td>
                        <td>{{$item->contractor->caption??""}}</td>
                        <td>{{$item->contractor_operation->caption??""}}</td>

                        <td>{{in_array($item->warehouse_type_id,[100])?$item->warehouse_type->caption:$item->warehouse->caption}}</td>

                        <td>{{in_array($item->productive_consume_warehouse_type_id,[2,100])?$item->productive_consume_warehouse_type->caption:$item->productive_consume_warehouse->caption}}</td>
                        <td>{{in_array($item->sampling_consume_warehouse_type_id,[2,100])?$item->sampling_consume_warehouse_type->caption:$item->sampling_consume_warehouse->caption}}</td>

                        <td>{{$item->delivery_unit->caption??""}}</td>
                        <td>{{$item->amount??""}}</td>
                        <td>{{$item->number}}</td>
                        <td>{{$item->percent_of_use}}</td>
                        <td>
                            <a href="{{route("line_product_station.product.bom_degree.index",[$product,$item->material_id,$item,$product_creation_process])}}">
                                {{$item->degrees()->count()}} درجه
                            </a>
                        </td>
                        <td>{{$item->waste_prediction}} %</td>
                        <td>{{$item->consumption_correction_factor}}</td>
                        <td>{{$item->consumption_correction_factor_prediction}}</td>
                        <td>
                            @if($item->has_material_goods_kind_fault())
                                <a href="{{route("line_product_station.product.bom_fault_illegal.index",[$product,$item->material_id,$item,$product_creation_process])}}">

                                    @php $bom_fault_illegals=$item->bom_fault_illegals()->count();@endphp
                                    @if($bom_fault_illegals==0)
                                        انتخاب نقص
                                    @else
                                        {{$bom_fault_illegals}}
                                        نقص غیرمجاز
                                    @endif
                                </a>
                            @else
                                ندارد
                            @endif
                        </td>
                        <td>
                            <a href="{{route("line_product_station.product.bom_item.destroy",$item)}}"
                               onclick="return confirm('آیا از حذف اطمینان دارید؟')" class="text-danger"><i
                                        class="fa fa-trash"></i> </a>
                            <a href="{{route("line_product_station.product.bom_item.edit",[$item,$product_creation_process??null])}}"
                                ><i
                                        class="fa fa-edit"></i> </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
        @break

@endswitch


<script>
    function formatUpTo9(num) {
        if (!isFinite(num)) return String(num);
        // اول با maximumFractionDigits قالب‌بندی می‌کنیم
        const s = new Intl.NumberFormat('en-US', {
            useGrouping: false,
            maximumFractionDigits: 9,
            notation: 'standard'
        }).format(num);
        // اگر خروجی شامل نقطه اعشار بود، صفرهای آخر را حذف کن
        return s.indexOf('.') >= 0 ? s.replace(/(\.\d*?)0+$/, '$1').replace(/\.$/, '') : s;
    }

</script>





{{--*/--}}
