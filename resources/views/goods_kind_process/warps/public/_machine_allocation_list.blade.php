@if(count($production->machine_allocation) > 0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> ماشین های تخصیص داده شده به کارت</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>نام و کد ماشین</th>
                            <th>اقدام کننده</th>
                            <th>زمان تخصیص</th>
                            <th> وضعیت تولید ماشین</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                       @foreach($production->machine_allocation as $item)
                                                    <tr>
                                                        <td>{{$row++}}</td>
                                                        <td>
                                                            <a href="{{route("production.machine.view",$item->machine->id)}}">{{$item->machine->code}}
                                                                - {{$item->machine->caption}}</a>
                                                        </td>

                                                        <td>{{$item->worker->fullname()}}</td>
                                                        <td>{{$item->get_datetime()}}</td>
                                                        <td>{{$item->machine->production_status->caption??"*"}}</td>
                                                @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
@endif
@if(count($production->machine_reserve) > 0)
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> ماشین های رزرو شده برای کارت</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling" style="text-align: center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>نام و کد ماشین</th>
                            <th>تعداد باند</th>
                            <th>اقدام کننده</th>
                            <th>زمان تخصیص</th>
                            <th> وضعیت تولید ماشین</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @php $allow_cancel_card= $post_user->checkButtonPermission("warps.allocation_cancel.index");@endphp
                        @foreach($production->machine_reserve as $item)
                            <tr>
                                <td>{{$row++}}</td>
                                <td>
                                    <a href="{{route("production.machine.view",$item->machine->id)}}">{{$item->machine->code}}
                                        - {{$item->machine->caption}}</a>
                                </td>
                                <td>{{$item->getNumberOfBand("reserve")}}</td>

                                <td>{{$item->worker->fullname()}}</td>
                                <td>{{$item->get_datetime()}}</td>
                                <td>{{$item->machine->production_status->caption??"*"}}</td>
                                <td>
                                    @if( $allow_cancel_card)
                                    <a href="{{route("warps.allocation_cancel.index",[$item->allocation_id,$item->production_id])}}" onclick="return confirm('آیا از کنسل کردن تخصیص اطمینان دارید؟');"><i class="fa fa-trash text-danger"></i> </a>
                                @endif
                                </td>
                        @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>

@endif


