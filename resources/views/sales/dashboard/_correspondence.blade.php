<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h5>مکاتبات با مشتری </h5>
        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>تاریخ و ساعت</th>
                        <th> اقدام کنننده</th>
                        <th>توضیحات </th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=0;@endphp
                    @foreach($order->order_logs as $item)
                        @if($item->customer_message )
                            <tr>
                                <td>{{++$row}}</td>
                                <td>{{$item->get_datetime()}} </td>
                                <td>{{$item->user->fullname()??""}}</td>

                                <td   style="width: 70%">{{$item->customer_message->text??""}}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="4">
                            <a href="{{route("sales.dashboard.log",$order)}}"> <i class="fa fa-bars"></i> مشاهده همه مکاتبات </a>
                        </td>
                    </tr>
                    </tbody>

                </table>
            </div>

        </div>

    </div>

</div>
