<div class="table-responsive">
    <table class="table table-styling center" style="">
        <thead>
        <tr>
            <th>ردیف</th>
            <th>تصویر کالا</th>
            <th>کد کالا</th>
            <th> عنوان کالا</th>
            <th> درجه کالا</th>
            <th> واحد سنجش</th>
            <th> حداقل تعداد
                <br/>
                بسته بندی
            </th>
            <th> مقدار درخواست</th>
            <th> مقدار تحویل شده</th>
            <th> مقدار باقی مانده</th>
            <th>مقدار انتخاب شده <br/>جهت خروج</th>
            <th> مقدار در حال تحویل</th>
            <th>نوع بسته بندی</th>
        </tr>

        </thead>
        <tbody>
        @php $row=0;@endphp
        @foreach($product_request_form->items as $item)
            <tr>
                <td>{{++$row}}</td>
                <td>

                    <a href="" data-lightbox="1" data-title="My caption 1">
                        <img src="" style="width: 50px" alt="" class="img-fluid img-thumbnail">
                    </a>

                </td>
                <td>{{$item->product->code}}</td>
                <td>{{$item->product->caption}}</td>
                <td>
                    @if($item->product_request_form_packing_types()->distinct("degree_id")->count()!=1)
                        <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-haspopup="true" style="width: 140px" aria-expanded="false">
                            {{$item->product_request_form_packing_types()->distinct("degree_id")->count()}}
                            نوع درجه
                        </button>
                        <div class="dropdown-menu" style="text-align: center">

                            @foreach($item->product_request_form_packing_types()->groupby("degree_id")->get() as $item_p)
                                <a href="#12" class="dropdown-item"
                                   id="btn_confirm_print4">  {{$item_p->degree->caption}}</a>
                            @endforeach


                        </div>
                    @else
                        {{$item->product_request_form_packing_types()->groupby("degree_id")->first()->degree->caption}}
                    @endif
                </td>
                <td>{{$item->product->unit->caption}}</td>
                <td>{{$item->min_number_of_packing_forms}}</td>
                <td>{{$item->amount_request}}</td>
                <td>
                    {{round($item->amount_sent,2)}}

                </td>
                <td>{{$item->amount_remaining}}</td>
                <td>
                    @if(!isset($item->amount_remaining) || $item->amount_remaining !=0 )

                        @if(in_array( $product_request_form->status_id, [7005001,7005005,7005004,7005008]))
                            <a href="{{route("wh.out.delivery.index",[$product_request_form,$item->product_id,$page])}}">
                                <i
                                    class="fa fa-plus"></i> </a>
                            {{$selected_amount[$item->id]["CurrentSelectedToExist"]["count"]}} بسته
                            ,
                            {{$selected_amount[$item->id]["CurrentSelectedToExist"]["amount"]+0}}
                            {{$item->product->unit->caption}}


                        @endif
                    @endif
                    {{--                    @if(!in_array( $product_request_form->status_id, [7005001,7005005,7005004]))--}}
                    {{--                        {{$item->getDeliveringAmount()}}--}}
                    {{--                        {{$item->product->unit->caption}}--}}
                    {{--                    @endif--}}
                </td>
                <td>
                    @if($selected_amount[$item->id]["CurrentDelivery"]["packing_form_count"]>0)
                        {{$selected_amount[$item->id]["CurrentDelivery"]["packing_form_count"]+0}}
                        بسته,
                        {{$selected_amount[$item->id]["CurrentDelivery"]["sum_amount"]+0}}
                        {{$item->product->unit->caption}}
                    @endif
                </td>
                <td>
                    @if($item->product_request_form_packing_types()->distinct("packing_type_id")->count()!=1)
                        <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-haspopup="true" style="width: 140px" aria-expanded="false">
                            {{$item->product_request_form_packing_types()->distinct("packing_type_id")->count()}}
                            بسته بندی مجاز
                        </button>
                        <div class="dropdown-menu" style="text-align: center">

                            @foreach($item->product_request_form_packing_types()->groupby("packing_type_id")->get() as $item_p)
                                <a href="#12" class="dropdown-item"
                                   id="btn_confirm_print4">  {{$item_p->packing_type->caption}}</a>
                            @endforeach


                        </div>
                    @else
                        {{$item->product_request_form_packing_types()->groupby("packing_type_id")->first()->packing_type->caption}}
                    @endif

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
