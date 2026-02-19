@if(count($replacement_list)>0)
    <div class="row">

        <div class="col-sm-12">


            <div class="table-responsive">
                <table class="table table-styling" style="text-align: center!important;">
                    <thead>
                    <tr>
                        <td>ردیف</td>
                        <th>شماره جابجایی شیفت</th>
                        <th>درخواست دهنده</th>
                        <th>زمان ثبت</th>
                        <th>زمان شروع جابجایی </th>
                        <th>زمان پایان جابجایی </th>
                        <th>جانشین</th>
                        <th>وضعیت</th>
                        <th></th>
                        <th></th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$replacement_list->firstItem();@endphp
                    @foreach($replacement_list as $item)
                        <tr>
                            <td title="{{$item->id}}">  {{$row++}}</td>
                            <td>{{$item->getCode()}}</td>
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
                                @if($item->leave_overtime_type_id == 400)
                                    <a href="{{route("hr.personal.leave.log",$item)}}">
                                        {{$item->getStatus()}}
                                    </a>
                                @else
                                    برگشت جابجایی
                                    {{$item->replacement_leave_overtime->getCode()}}
                                @endif

                            </td>
                            <td>
                                @if($worker_id_equal_auth_id )
                                    @if($item->status_id == 4630009)
                                        <button
                                            class="btn btn-primary btn-sm text-white  md-trigger md-setperspective"
                                            data-modal="modal-15" href="#!"
                                            onclick="setLeaveId({{$item->id}},'add_comment')"><i
                                                class="fa fa-comment"></i> ثبت توضیحات
                                        </button>
                                    @endif
                                    @if( !in_array($item->status_id, [4630003,4630004,4630005,4630007,4630006,4630008]))
                                        <a
                                            class="btn btn-danger btn-sm text-white  "
                                            href="{{route("hr.personal.replacement.cancel",$item)}}"
                                            onclick="return confirm('آیا از انصراف  جابجایی شیفت اطمینان دارید؟')"><i
                                                class="fa fa-times"></i> انصراف
                                        </a>
                                    @endif
                                @endif
                            </td>
                            <td>

                            </td>
                        </tr>

                    @endforeach
                    </tbody>

                </table>
            </div>
            <div class="float-left">
                نمايش رکوردهای
                <b>{{$replacement_list->firstItem()}}</b>
                تا
                <b>{{$replacement_list->lastItem()}}</b>
                از
                <b>{{$replacement_list->total()}}</b>
                رکورد موجود
            </div>
            <br/>
            <div class="text-center">
                {{$replacement_list->links('pagination::bootstrap-4')}}
            </div>
        </div>

    </div>

@endif
