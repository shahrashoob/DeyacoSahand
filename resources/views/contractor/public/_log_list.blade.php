
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>سابقه عملیات بر روی دستور پیمان
            </h5>
        </div>
        <div class="card-block">

            <div class="table-responsive">
                <table class="table table-styling" style="text-align: center!important;">
                    <thead>
                    <tr>
                        <td>ردیف</td>
                        <th>تاریخ و ساعت</th>
                        <th>اقدام کننده</th>
                        <th>رویداد</th>
                        <th>وضعیت</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=1;@endphp
                    @foreach($contractor_allocation->logs as $item)
                        <tr>
                            <td title="{{$item->id}}">{{$row++}}</td>
                            <td>
                                {{$item->get_datetime()}}
                            </td>
                            <td>
                                {{$item->worker->fullname()}}
                            </td>
                            <td>
                                {{$item->event->caption??""}}
                            </td>
                            <td>
                                {{$item->machine_allocation_status->caption??""}}
                            </td>
                        </tr>

                        @if($item->message)
                            <tr>
                                <td colspan="4" class="alert alert-warning">
                                    {{$item->message->text??""}}
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
