<div class="table-responsive">
    <table class="table table-styling center" style="">
        <thead>
        <tr>
            <th>ردیف</th>
            <th>تصویر کالا</th>
            @if(isset($type) && $type=="out.customer")
                <td>
                    کد درخواست
                </td>
            @endif
            <th>کد کالا</th>
            <th> عنوان کالا</th>
            <th></th>
            <th>مقدار انتخاب شده <br/>جهت خروج</th>
            <th></th>
            <th> درجه کالا</th>
            <th> واحد سنجش</th>
            <th> حداقل/حداکثر تعداد
                <br/>
                بسته بندی
            </th>
            <th> مقدار درخواست</th>
            <th></th>
            <th> مقدار تحویل شده</th>
            <th> مقدار باقی مانده</th>
            <th> مقدار در حال تحویل</th>
            <th>نوع بسته بندی</th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($product_request_form_items as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>

                    <a href="" data-lightbox="1" data-title="My caption 1">
                        <img src="" style="width: 50px" alt="" class="img-fluid img-thumbnail">
                    </a>

                </td>
                @if(isset($type) && $type=="out.customer")
                    <td>
                       {{$item->product_request_form_code}}
                    </td>
                @endif
                <td>{{$item->product->code}}</td>
                <td>{{$item->product->caption}}          </td>
                <td>
                    <div class="spinner-border text-primary spinner-border-sm" role="status">
                        <span class="sr-only">

                                    @if(isset($allow_select_product) && $permission_confirm)
                                <a href="{{route("wh.out.delivery.index",[$item->product_request_form_id,$item->product_id,$page??1])}}">
                                    <i
                                            class="fa fa-plus"></i> </a>
                            @endif


                        </span>
                    </div>
                </td>
                <td>
                    <div class="spinner-border text-primary spinner-border-sm" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </td>
                <td>
                    <a title="تغییر در درخواست"
                       href="{{route("utility.special_license.panel.new_special_license.index",[8,$item->product_request_form_id,$item->id])}}">
                        <i class="fa fa-unlock-alt"></i>
                        <i style="margin-right: -5px; " class="fa  fa-exchange-alt"></i>

                    </a>
                </td>
                <td>

                </td>
                <td>{{$item->product->unit->caption??"---"}}</td>
                <td>{{$item->min_number_of_packing_forms?? "-"}} / {{$item->max_number_of_packing_forms?? "-"}}</td>
                <td>{{$item->amount_request}}</td>

                <td>

                </td>

                <td>
                    {{round($item->amount_sent,4)}}

                </td>
                <td>{{$item->amount_remaining}}</td>
                <td>

                </td>

                <td>

                    <a title="اضافه کردن بسته بندی جدید"
                       href="{{route("utility.special_license.panel.new_special_license.index",[4,$item->product_request_form_id,$item->id])}}"><i
                                class="fa fa-unlock-alt"></i></a>
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="15">
                <br/>
            </td>
        </tr>
        </tbody>
    </table>
</div>
