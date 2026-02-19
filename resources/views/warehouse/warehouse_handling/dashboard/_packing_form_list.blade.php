<div class="table-responsive">
    <table class="table table-styling center">
        <thead>
        <tr>
            <th>#</th>

            <th>کد بسته بندی</th>
            <th>انبار</th>
            <th>مقدار کل</th>
            @if($warehouse_handling->check_diff_in_amount)
                <th>مقدار کل (انبارگردانی) </th>
            @endif
            <th>وضعیت بسته بندی</th>
            <th>وضعیت انبار گردانی</th>
            <th></th>
        </tr>

        </thead>
        <tbody>
        @php $row=$list->firstItem();@endphp
        @foreach($list as $item)
            <tr>
                <td>{{$row++}}</td>
                <td>
                    <a href="{{route("fabric_raw.packing_form.view",[$item->packing_form_id,1,"wh.warehouse_handling.dashboard.packing_form_list",$warehouse_handling->id,$item->status_id])}}">
                        {{$item->packing_form->code??($item->packing_form_id+1000)}}
                    </a>
                </td>
                <td>{{$item->packing_form->warehouse->caption??""}}</td>
                <td class='{{$item->has_diff_in_amount?"text-danger":""}}'>{{$item->packing_form?$item->packing_form->getFinalAmount():""}}</td>
                @if($warehouse_handling->check_diff_in_amount)
                    <td class='{{$item->has_diff_in_amount?"text-danger":""}}' >{{$item->final_amount}}  </td>
                @endif
                <td>{{$item->packing_form->status->caption??""}}</td>
                <td>{{$item->status->caption??""}}</td>
                <td>
                    @if($permission_add_packing_form  && in_array($item->status_id , [524000407]))
                        <a href="{{route("wh.warehouse_handling.end_of_review.check_packing_form",[$warehouse_handling,$item])}}"
                           onclick="return confirm('آیا از اصلاح وضعیت بسته بندی اطمینان دارید؟')">
                           اصلاح وضعیت
                        </a>

                    @endif
                    @if($permission_add_packing_form  && in_array($item->status_id , [524000403]))
                        <a href="{{route("wh.warehouse_handling.end_of_review.reading_packing_form_after",[$warehouse_handling,$item])}}"
                           >
                           خواندن بسته بندی
                        </a>

                    @endif
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
