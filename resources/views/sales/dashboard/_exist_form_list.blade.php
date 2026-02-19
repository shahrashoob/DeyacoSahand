@php $list=$order->getExitFormList();@endphp
@if(count($list)>0)
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5> لیست برگ (های) خروج از انبار</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th> فرم درخواست کالا</th>
                            <th>وضعیت درخواست</th>
                            <th> برگ خروج</th>
                            <th>تاریخ ارسال</th>
                            <th>تعداد بسته بندی</th>
                            <th>وضعیت برگ خروج</th>
                            <th>برگ خروج ریالی</th>
                            <th>فاکتور</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $i=1; $old_prf=[];@endphp
                        @foreach($list as $item)
                            @php $old_prf[$item->product_request_form_id]=$item->product_request_form_id;@endphp
                            <tr>
                                <td>{{$i++}}</td>
                                <td>
                                    <a href="{{route("sales.dashboard.view_product_request_form",[$order,$item->product_request_form_id??0])}}">
                                        {{$item->product_request_form_code}}
                                    </a>
                                </td>
                                <td>
                                    {{$product_request_forms[$item->product_request_form_id]->status->caption}}
                                </td>
                                <td>
                                    <a href="{{route("sales.dashboard.view_form",[$order,$item->id])}}">
                                        {{$item->code}}
                                    </a>
                                </td>
                                <td>{{$item->get_create_date_and_time()}}</td>
                                <td>{{count($item->getPackingFormList()->toArray())}}</td>
                                <td>
                                    @if($item->status_id ==500000520  &&    $post_user->checkButtonPermission("sales._confirm_financial_unit") )
                                        {{--   در انتظار تایید نهایی --}}
                                        <a class=""
                                           href="{{route("sales.confirmation_of_financial_unit.index",[$order,$item->id])}}">
                                            ثبت تایید نهایی
                                        </a>
                                    @elseif($item->status_id ==500000515  &&    $post_user->checkButtonPermission("sales._confirm_draft_form") )
                                        {{--   در انتظار تایید واحد مالی --}}
                                        <a class=""
                                           href="{{route("sales.confirmation_of_draft_form.index",[$order,$item->id])}}">
                                            ثبت تایید پیش نویس
                                        </a>
                                    @elseif($item->status_id ==500000514  &&    $post_user->checkButtonPermission("sales._confirm_demands_form") )
                                        {{--   در انتظار تایید وصول مطالبات --}}
                                        <a class=""
                                           href="{{route("sales.confirmation_of_demands_form.index",[$order,$item->id])}}">
                                            ثبت تایید وصول مطالبات
                                        </a>

                                    @elseif($item->status_id ==500000500  &&    $post_user->checkButtonPermission("sales._confirm_customer_form") )
                                        {{--   در انتظار تایید مشتری --}}
                                        <a class=""
                                           href="{{route("sales.confirmation_of_customer_form.index",[$order,$item->id])}}">
                                            ثبت تایید از طرف مشتری
                                        </a>
                                    @else
                                        {{$item->status->caption}}
                                    @endif

                                </td>
                                <td>

                                    <a class="" href="{{route("sales.print.exit_form_pre_factor",[$order,$item->id])}}">
                                        <i class="fa fa-download"></i>
                                    </a>

                                </td>
                                <td>
                                    @if($item->hasWarehouseTransaction() )
                                        <a class="" href="{{route("sales.print.exit_form_factor",[$order,$item->id])}}">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @foreach ($product_request_forms as $prf_item)
                            @if(!in_array($prf_item->id,$old_prf))
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>
                                        <a href="{{route("sales.dashboard.view_product_request_form",[$order,$prf_item->id])}}">
                                            {{$prf_item->code}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$prf_item->status->caption}}
                                    </td>
                                    <td>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>


                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
