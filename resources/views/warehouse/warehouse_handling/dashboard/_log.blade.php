{{--@if($post_user->checkButtonPermission("production.view_log"))--}}
@if($warehouse_handling->logs()->count()>0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> سابقه عملیات انبارگردانی {{$warehouse_handling->getCode()}}</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>تاریخ و ساعت</th>
                            <th>اقدام کننده</th>
                            <th>رویداد</th>
                            <th>وضعیت</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($warehouse_handling->logs as $item)
                            <tr>
                                <td title="{{$item->id}}">{{$row++}}</td>

                                <td>{{$item->get_datetime()}}</td>
                                <td>{{$item->worker->fullname()}}</td>
                                <td>
                                        {{$item->event->caption??""}}
                                </td>
                                <td>{{$item->status->caption}}</td>

                            </tr>

                            @if(isset($item->message->text))
                                <td colspan="5" style="padding: 0">
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
        </div>
    </div>
@endif
