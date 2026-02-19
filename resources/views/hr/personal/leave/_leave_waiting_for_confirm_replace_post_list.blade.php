@if(count($leave_waiting_confirm)>0 && count($leave_waiting_confirm->where("leave_overtime_type_id","!=",300))>0)
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست درخواست های مرخصی در انتظار تایید
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>شماره مرخصی</th>
                                <th>نوع مرخصی</th>
                                <th>درخواست دهنده</th>
                                <th>زمان ثبت</th>
                                <th>زمان شروع مرخصی</th>
                                <th>زمان پایان مرخصی</th>
                                <th>جانشین</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$leave_waiting_confirm->firstItem();@endphp
                            @foreach($leave_waiting_confirm->where("leave_overtime_type_id","!=",300) as $item)
                                <tr>
                                    <td title="{{$item->id}}">  {{$row++}}</td>
                                    <td>{{$item->getCode()}}</td>
                                    <td>{{$item->leave_overtime_type->caption??""}}</td>
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
                                        {{$item->getStatus()}}
                                    </td>
                                    <td>
                                        <a href="{{route("hr.personal.leave.confirm_replace_user",$item)}}"
                                           class="btn btn-primary btn-sm text-white"
                                           onclick="return confirm('آیا متعهد می شوید کلیه وظایف آقای/خانم {{$item->worker->fullname()}} در زمان نبود ایشان به صورت کامل انجام دهید؟ ');">تایید</a>
                                        <a href="{{route("hr.personal.leave.reject_replace_user",$item)}}"
                                           class="btn btn-danger btn-sm text-white"
                                           onclick="return confirm('آیا از عدم تایید جانشینی مرخصی اطمینان دارید؟ ');">عدم تایید</a>
                                    </td>
                                </tr>


                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$leave_waiting_confirm->firstItem()}}</b>
                        تا
                        <b>{{$leave_waiting_confirm->lastItem()}}</b>
                        از
                        <b>{{$leave_waiting_confirm->total()}}</b>
                        رکورد موجود
                    </div>
                </div>
                <div class="text-center">
                    {{$leave_waiting_confirm->links('pagination::bootstrap-4')}}
                </div>
            </div>

        </div>
    </div>
@endif
