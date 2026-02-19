<div class="col-md-12">
    <table class="table table-styling center">
        <thead>
        <tr>
            <th colspan="7">
                لیست بسته بندی های انتخاب شده
            </th>
        </tr>
        <tr>
            <th>d#</th>
            <th> کد بسته بندی</th>
            <th></th>
            <th>نوع بسته بندی</th>

            <th>حامل</th>
            <th>تعداد آیتم</th>
            <th> تعداد بسته بندی انتخاب شده</th>
            <th>تنوع آیتم</th>
{{--            <th></th>--}}
        </tr>

        </thead>
        <tbody>
        @php $row=$selected_packing_list->firstItem();@endphp
        @foreach($selected_packing_list as $item)
            <tr>
                <td>{{$row++}}</td>

                <td>{{$item->getCode()}}</td>
                <td>
                    {{$item->packing_type->fullCaption()}}
                    @if(isset($count_select[$item->id]) || isset($amount_select[$item->id]))
                        <div class="error">بعد از تحویل این بسته بندی باز می شود
                        &nbsp;
                        &nbsp;
                            @if(isset($count_select[$item->id]))
                                <i class="fa fa-check"></i>
                                {{($count_select[$item->id])}} بسته بندی کالا جدا می شود
                            @endif
                            @if(isset($amount_select[$item->id]))
                                <i class="fa fa-check"></i>
                                    مقدار {{$amount_select[$item->id]}}

                            {{$item->getUnitCaption("unit","caption")}}
                                از بسته بندی جدا می شود.
                            @endif

                        </div>
                    @endif
                    @if(isset($need_new_packing[$item->id]->id))
                        <div class="error">
                            بسته بندی جدید:
                            {{$need_new_packing[$item->id]->fullCaption()}}
                        </div>
                    @endif
                </td>
                <td>{{$item->carrier->code??""}}</td>

                <td>{{$item->getItemCount("items")}}</td>
                <td>
                    {{isset($count_select[$item->id])?$count_select[$item->id]." از ":""}}
                    {{$item->getItemCount("packing_form_contents")}}

                </td>
                <td>{{$item->items()->distinct("product_id")->count("id")}}</td>

{{--                <td>--}}
{{--                    <a class="m-t-5 collapsed"--}}
{{--                       data-toggle="collapse" href="#packing_{{$item->id}}"--}}
{{--                       role="button" aria-expanded="false"--}}
{{--                       aria-controls="packing_{{$item->id}}">--}}
{{--                        <i class="fa fa-eye"></i>--}}
{{--                    </a>--}}

{{--                </td>--}}
            </tr>
{{--            <tr>--}}
{{--                <td colspan="7">--}}
{{--                    <div class="collapse " id="packing_{{$item->id}}" style="">--}}
{{--                        <div class="row " style="border: 3px solid #efefef">--}}


{{--                            <div class="table-responsive">--}}
{{--                                <table class="table table-styling">--}}
{{--                                    <thead>--}}
{{--                                    <tr>--}}
{{--                                        <th colspan="6">--}}
{{--                                            <h5>لیست آیتم های موجود در بسته--}}
{{--                                                بندی {{$item->code}}</h5>--}}
{{--                                        </th>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <th>#</th>--}}
{{--                                        <th>ردیف</th>--}}
{{--                                        <th>کد کالا</th>--}}
{{--                                        <th>نام کالا</th>--}}
{{--                                        <th>{{$item->getUnitCaption("unit","measurement")}}</th>--}}
{{--                                        <th>{{$item->getUnitCaption("sub_unit","measurement")}}</th>--}}
{{--                                    </tr>--}}

{{--                                    </thead>--}}
{{--                                    <tbody>--}}
{{--                                    @php $row_packing_form_item=1;@endphp--}}
{{--                                    @foreach($item->items as $packing_item)--}}
{{--                                        <tr>--}}
{{--                                            <td>{{$row_packing_form_item++}}</td>--}}
{{--                                            <td>{{$packing_item->production_form_item->code??""}}</td>--}}
{{--                                            <td>{{$packing_item->product->code}}</td>--}}
{{--                                            <td>{{$packing_item->product->caption}}</td>--}}
{{--                                            <td>{{$packing_item->final_amount}}</td>--}}
{{--                                            <td>{{$packing_item->sub_amount}}</td>--}}
{{--                                    @endforeach--}}
{{--                                    </tbody>--}}

{{--                                </table>--}}
{{--                            </div>--}}


{{--                        </div>--}}
{{--                    </div>--}}
{{--                </td>--}}
{{--            </tr>--}}
        @endforeach

        </tbody>
    </table>
</div>
<div class="col-md-12">
    <div class="text-center">
        نمايش رکوردهای
        <b>{{$selected_packing_list->firstItem()}}</b>
        تا
        <b>{{$selected_packing_list->lastItem()}}</b>
        از
        <b>{{$selected_packing_list->total()}}</b>
        رکورد موجود


    </div>
    <div class="text-center">
        {{$selected_packing_list->links('pagination::bootstrap-4')}}
    </div>
</div>
