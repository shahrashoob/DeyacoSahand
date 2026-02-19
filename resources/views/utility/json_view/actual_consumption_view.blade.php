@extends('layouts.admin._master')
@section("page_header_title","مدیریت اطلاعات  ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> جزئیات محاسبات مقدار واقعی شماره
                        {{$json_data->machine_allocation_modification??""}}
                    </h5>
                </div>
                <div class="card-block">
                    @php $row=1;@endphp
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>توضیحات</th>
                                <th>مقدار</th>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>نتیجه اجرا</td>
                                <td>{{isset($json_data->result)&& $json_data->result?"موفق":"نا موفق"}}
                                    - {{$json_data->message??""}}</td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>نام و کد کالا</td>
                                <td>{{$json_data->product->code??""}} - {{$json_data->product->caption??""}}</td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>از تخصیص شماره</td>
                                <td>{{$json_data->start_allocation??""}}</td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>تا تخصیص شماره</td>
                                <td>{{$json_data->end_allocation_id??""}}</td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>مقدار تراکنش مصرف سامانه به ازای هر تخصیص</td>
                                <td>
                                    @if(isset($json_data->allocations_consumed))
                                        @foreach($json_data->allocations_consumed as $allocation_id=>$sum_amount)

                                            تخصیص شماره {{$allocation_id}} : <b>{{$sum_amount}}</b>
                                            <br/>

                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>مقدار مصرف واقعی که قبلا به ازای هر تخصیص محاسبه شده است</td>
                                <td>
                                    @if(isset($json_data->calculated_actual_consumption))
                                        @foreach($json_data->calculated_actual_consumption as $allocation_id=>$sum_amount)

                                            تخصیص شماره {{$allocation_id}} : <b>{{$sum_amount}}</b>
                                            <br/>

                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>جمع تراکنش های اصلاحی ورود برای بسته بندی های برگشت زده شده</td>
                                <td>{{$json_data->amendment_input_sum??""}}</td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>جمع تراکنش های اصلاحی خروج برای بسته بندی های برگشت زده شده</td>
                                <td>{{$json_data->amendment_output_sum??""}}</td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>مقدار تراکنش اصلاحی</td>
                                <td>{{$json_data->amendment??""}}</td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>جمع کل تغییر درجه داده شده</td>
                                <td>{{$json_data->change_degree_and_waste->change_degree_sum??""}}</td>
                            </tr>
                            <tr>
                                <td>{{$row++}}</td>
                                <td>جمع کل ضایعات</td>
                                <td>{{$json_data->change_degree_and_waste->waste??""}}</td>
                            </tr>
                            @if(isset($json_data->update_actual_amount))
                                @foreach($json_data->update_actual_amount as $allocation_id=>$update_actual_amount)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            جزییات محاسبه مقدار واقعی برای تخصیص
                                            {{$allocation_id}}
                                        </td>
                                        <td>
                                            نتیجه: <b>{{$update_actual_amount->result? "موفق":"ناموفق"}}</b>

                                            <br/>
                                            @if(isset($update_actual_amount->error))
                                                پیام خطا: <b>{{$update_actual_amount->error}}</b>
                                            @endif
                                            <br/>
                                            مقدار واقعی محاسبه شده: <b>{{$update_actual_amount->actual_amount??""}}</b>
                                            <br/>
                                            مقدار تغییر درجه داده شده:
                                            <b>{{$update_actual_amount->change_degree_amount??""}}</b>
                                            <br/>
                                            مقدار کل تخصیص(تاکنون):
                                            <b>{{$update_actual_amount->calculated_amount_till_now??""}}</b>

                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-dark"> بازگشت</a>
                    </div>

                </div>

            </div>
        </div>


    </div>

@endsection
@section("styles")

@endsection


