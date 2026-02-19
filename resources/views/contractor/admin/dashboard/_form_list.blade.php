<div class="col-sm-12">

    <div class="card">
        <div class="card-header">
            <h5> لیست فرم های تحویل مواد اولیه </h5>
        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling center" style="">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>شماره فرم درخواست کالا</th>
                        <th>شماره فرم ارسال کالا</th>
                        <th>تاریخ ارسال</th>
                        <th> تعداد بسته</th>
                        <th>وضعیت</th>
                        <th></th>
                    </tr>

                    </thead>
                    <tbody>
                    @if(count($product_request_form_list)>0)
                        @php $row=0;@endphp
                        @foreach($product_request_form_list as $product_form)
                            @if($product_form->forms()->count()==0)
                                <td>{{++$row}}</td>
                                <td> {{$product_form->getCode()}} </td>
                                <td colspan="5">  </td>
                            @else
                                @foreach($product_form->forms as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>

                                            {{$product_form->getCode()}}


                                        </td>
                                        <td>
                                            <a href="{{route("contractor.admin.dashboard.view_form",[$contractor_allocation,$item->form])}}">
                                                {{$item->form->code}}
                                            </a>

                                        </td>
                                        <td>{{$item->form->get_create_date_and_time()}}</td>
                                        <td>{{count($item->form->getPackingFormList()->toArray())}}</td>
                                        <td>
                                            @if($item->form->status_id ==500000520  &&    $post_user->checkButtonPermission("contractor._confirm_financial_unit") )
                                                {{--   در انتظار تایید نهایی --}}
                                                <a class=""
                                                   href="{{route("contractor.admin.confirmation_of_financial_unit.index",[$contractor_allocation,$item->form])}}">
                                                    ثبت تایید نهایی
                                                </a>
                                            @elseif($item->form->status_id ==500000515  &&    $post_user->checkButtonPermission("contractor._confirm_draft_form") )
                                                {{--   در انتظار تایید واحد مالی --}}
                                                <a class=""
                                                   href="{{route("contractor.admin.confirmation_of_draft_form.index",[$contractor_allocation,$item->form])}}">

                                                    ثبت تایید پیش نویس
                                                </a>
                                            @else
                                                {{$item->form->status->caption}}
                                            @endif


                                        </td>
                                        <th>

                                        </th>
                                    </tr>
                                @endforeach
                            @endif

                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

