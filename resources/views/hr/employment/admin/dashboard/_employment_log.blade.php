@if($employment->logs()->count()>0)
    <div class="col-sm-12">
        <div class="table-responsive center">
            <table class="table table-styling">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th>تاریخ وساعت</th>
                    <th>اقدام کننده</th>
                    <th>رویداد</th>
                    <th>وضعیت درخواست همکاری</th>
                    <th></th>
                </tr>

                </thead>
                <tbody>
                @php
                    $row = 0;
                @endphp


                @foreach($employment->logs as $item)
                    <tr>
                        <td>{{++$row}}</td>

                        <td>{{$item->get_datetime()}}</td>
                        <td>{{$item->worker->fullname()}}
                        </td>
                        <td>{{$item->event->caption}}</td>
                        <td>{{$item->status->caption}}</td>
                        <td>{{$item->employment_selection->selection->caption ?? ""}}</td>
                    </tr>

                    @if(isset($item->message->text))
                        <tr>
                            <td colspan="7" style="padding: 0">
                                <div class="alert alert-info ">
                                    {!! $item->message->text??"" !!}
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif