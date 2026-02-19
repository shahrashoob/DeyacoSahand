<div class="card">
    <div class="card-header">
        <h5>لیست بسته بندی های انتخاب شده</h5>
    </div>
    <div class="card-block">

        <div class="row">
            <div class="table-responsive">
                <table class="table table-styling center" id="myTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th></th>
                        <th> کد بسته بندی</th>

                        <th>مقدار کل بسته بندی</th>
                        <th> تعداد بسته بندی فرعی</th>
                        <th>انتخاب کل (بخشی از) <br/> بسته بندی</th>
                        <th>بسته بندی حمل و نقل</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$selected_packing_list->firstItem();@endphp
                    @foreach($selected_packing_list as $item)
                        <tr>
                            <td>{{$row++}}</td>
                            <td>
                                <a href="{{route("wh.out.delivery.remove_packing_form_request",[$product_request_form,$item,$dashboard_type??""])}}" onclick="return confirm('آیا از حذف اطمینان دارید؟')"
                                    class="checked_packing_form"
                                    id="checked_packing_form_id_{{$item->id}}"
                                >
                                    <span class="text-danger"><i class="fa fa-trash"></i> </span> </a>
                            </td>
                            <td>{{$item->code}}</td>


                            <td>
                                @if($allow_select_partial_of_packing_in_output)
                                    <input
                                            class="amount_select_packing_form"
                                            id="packing_form_id_amount_{{$item->id}}"
                                            name="data[amount_select][{{$item->id}}]"
                                            type="number"
                                            min="1"
                                            max="{{$item->getFinalAmount()}}"

                                            value="{{isset($amount_select[$item->id])?$amount_select[$item->id]:$item->getFinalAmount()}}"
                                    >
                                @else
                                    {{$item->getFinalAmount()}}
                                @endif

                            </td>

                            <td>{{$item->sub_packing_form_number}}</td>
                            <td>
                                {{--                                || isset($packing_list_data[$item->id]["transport_item_code"])--}}
                                @if($item->sub_packing_form_number <=1 )
                                    کل بسته

                                @else
                                    <input
                                        class="count_select_packing_form"
                                        id="packing_form_id_{{$item->id}}"
                                        name="data[count_select][{{$item->id}}]"
                                        type="number"
                                        min="1"
                                        max="{{$item->sub_packing_form_number}}"

                                        value="{{isset($count_select[$item->id])?$count_select[$item->id]:$item->sub_packing_form_number}}"
                                    >
                                @endif
                            </td>
                            <td>

                                {{isset($packing_list_transport_item[$item->id])?$packing_list_transport_item[$item->id]:""}}
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            <div class="float-left">
                نمايش رکوردهای
                <b>{{$selected_packing_list->firstItem()}}</b>
                تا
                <b>{{$selected_packing_list->lastItem()}}</b>
                از
                <b>{{$selected_packing_list->total()}}</b>
                رکورد موجود


            </div>
        </div>


        <div style="text-align: center">
            <div class="text-center">
                {{$selected_packing_list->links('pagination::bootstrap-4')}}
            </div>


        </div>

    </div>
</div>
