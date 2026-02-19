@if(count($leave_waiting_confirm_post)>0)
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست درخواست های در انتظار تایید
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>
                                <td>ردیف</td>
                                <th>شماره درخواست</th>
                                <th>نوع درخواست</th>
                                <th>درخواست دهنده</th>
                                <th>زمان ثبت</th>
                                <th>زمان شروع </th>
                                <th>زمان پایان </th>
                                <th>جانشین</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($leave_waiting_confirm_post as $item)
                                <tr>
                                    <td title="{{$item->id}}">  {{$row++}}</td>
                                    <td>{{$item->getCode()}}</td>
                                    <td>{{$item->leave_overtime_type->caption}}</td>
                                    <td>{{$item->worker->fullname()}}</td>
                                    <td>
                                        {{$item->get_created_at()}}
                                    </td>
                                    <td>
                                        {{$item->get_start_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->get_end_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->get_replace_worker()}}
                                    </td>
                                    <td>
                                        <a href="{{route("hr.personal.leave.log",$item)}}">
                                            {{$item->getStatus()}}
                                        </a>
                                    </td>
                                    <th>
                                        <button class="btn btn-primary btn-sm text-white  md-trigger md-setperspective"
                                                data-modal="modal-16" href="#!"
                                                onclick="setLeaveId({{$item->id}},'confirm')">
                                            <i class="fa fa-check"></i> تایید
                                        </button>
                                        <button class="btn btn-primary btn-sm text-white  md-trigger md-setperspective"
                                                data-modal="modal-17" href="#!"
                                                onclick="setLeaveId({{$item->id}},'comment')"><i
                                                class="fa fa-comment"></i> اخذ توضیح
                                        </button>
                                        <button class="btn btn-danger btn-sm text-white  md-trigger md-setperspective"
                                                data-modal="modal-19" href="#!"
                                                onclick="setLeaveId({{$item->id}},'reject')"><i
                                                class="fa fa-times"></i> عدم تایید
                                        </button>
                                    </th>

                                </tr>
                                <tr>
                                    <td colspan="10" class="alert-info">
                                        {!! $item->getText() !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endif
