@php $row=0;
                        $list=!isset($event_id)? $order->order_logs:$order->order_logs()->where("event_id",$event_id)->get();
@endphp
@if(isset($title) && count($list)==0)
@else

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>
                    @if(!isset($title)) لیست تغییر وضعیت ها - سفارش {{$order->code()}}

                    @else
                        {{$title}}
                    @endif

                </h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>تاریخ و ساعت</th>
                            <th> اقدام کنننده</th>
                            <th> عملیات</th>
                            <th> وضعیت</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>

                        @foreach($list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>{{$item->get_datetime()}} </td>
                                <td>{{$item->user->fullname()??""}}</td>
                                <td>
                                    {{$item->event->caption??""}}
                                </td>
                                <td>
                                    {{$item->getStatus()}}
                                </td>
                                <td>
                                    {{$item->form->code??""}}
                                </td>
                            </tr>
                            @if(isset($item->customer_message->text) || isset($item->message->text))
                                <tr>
                                    <td colspan="6" style="padding: 0">
                                        @if(isset($item->customer_message->text))

                                            <div class="alert alert-info ">
                                                {!! $item->customer_message->text??"" !!}
                                            </div>

                                        @endif
                                        @if(!$is_customer && isset($item->message->text))

                                            <div class="alert alert-warning ">
                                                {!! $item->message->text??"" !!}
                                            </div>

                                        @endif
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
