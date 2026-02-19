@if(count($order->consumedProduct) > 0)
    <div class="col-md-12">

        <div class="card">
            <div class="card-header">
                <h5>مشخصات نوع تامین مواد اولیه </h5>
            </div>
            <div class="card-block overflow-auto">
                <table class="table  " style="text-align: center;font-size:11px;width: 100%">
                    <thead>
                    <tr>
                        <th> ردیف</th>
                        <th>کد کالا</th>
                        <th>نام کالا</th>
                        <th>نام ماده اولیه</th>
                        <th>کد ماده اولیه</th>
                        <th>نوع تامین</th>
                        @if($order->customer->get_packing_form_details)
                            <th>فرم ورود به انبار</th>
                            <th>وضعیت فرم به انبار</th>
                        @endif
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=1;
                        $old_materials=[];
                    @endphp
                    @foreach($order->consumedProduct as $item)
                        @if(!isset( $old_materials[$item->material_id]))
                            @php $old_materials[$item->material_id]=1; @endphp
                            <tr>
                                <td>{{$row++}}</td>
                                <td>{{$item->product->code}}</td>
                                <td>{{$item->product->caption}}</td>
                                <td>{{$item->material->code}}</td>
                                <td>{{$item->material->caption}}</td>
                                <td>{{$item->contractor_supply_type->caption}}</td>
                                {{--                            در انتظار آماده سازی--}}
                                @if($order->status_id == 35030)
                                    <td>
                                        @if(isset($is_customer) && $is_customer)
                                            <a href="{{route("customer_group.order.sending_material.index",[$order,$item->material_id])}}" >ارسال مواد اولیه</a>
                                        @else
                                            <a href="{{route("sales.sending_material.index",[$order,$item->material_id])}}" >ارسال مواد اولیه</a>

                                        @endif
                                    </td>
                                @endif
                                @if($order->customer->get_packing_form_details)
                                    <td>{{$item->form_general_item->form->code??""}}</td>
                                    <td>{{$item->form_general_item->form->status->caption??""}}</td>
                                @endif

                            </tr>
                        @else

                        @endif

                    @endforeach

                    </tbody>
                </table>

            </div>

        </div>

    </div>
@endif
