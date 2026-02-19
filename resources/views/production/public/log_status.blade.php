@if($post_user->checkButtonPermission("production.view_log"))
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> سابقه عملیات بر روی کارت تولید</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>تاریخ و ساعت</th>
                            <th>وضعیت</th>
                            <th>اقدام کننده</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($production->get_log_with_status() as $item)
                            <tr>
                                <td>{{$row++}}</td>

                                <td>{{$item->get_datetime()}}</td>
                                <td>{{$item->getStatus()}}</td>
                                <td>{{$item->worker->fullname()}}</td>
                            </tr>

                            @if(isset($item->message->text))
                                <td colspan="4" style="padding: 0">
                                    <div class="alert alert-info ">
                                        {{$item->message->text??""}}
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
