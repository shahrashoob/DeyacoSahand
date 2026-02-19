@if(count($mission_list)>0)
    <div class="row">

        <div class="col-sm-12">


            <div class="table-responsive">
                <table class="table table-styling" style="text-align: center!important;">
                    <thead>
                    <tr>
                        <td>ردیف</td>
                        <th>شماره ماموریت</th>
                        <th>درخواست دهنده</th>
                        <th>زمان ثبت</th>
                        <th>زمان شروع اضافه کاری</th>
                        <th>زمان پایان اضافه کاری</th>
                        <th>وضعیت</th>
                        <th></th>
                        <th></th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$mission_list->firstItem();@endphp
                    @foreach($mission_list as $item)
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

                                <a href="{{route("hr.personal.leave.log",$item)}}">
                                    {{$item->getStatus()}}
                                </a>


                            </td>
                            <td>
                                @if($worker_id_equal_auth_id )
                                    @if($item->status_id == 4630009)
                                        <button
                                            class="btn btn-primary btn-sm text-white  md-trigger md-setperspective"
                                            data-modal="modal-15" href="#!"
                                            onclick="setLeaveId({{$item->id}},'add_commnet')"><i
                                                class="fa fa-comment"></i> ثبت توضیحات
                                        </button>
                                    @endif
                                    @if( !in_array($item->status_id, [4630003,4630004,4630005,4630007,4630006,4630008]))
                                        <a
                                            class="btn btn-danger btn-sm text-white  "
                                            href="{{route("hr.personal.mission.cancel",$item)}}"
                                            onclick="return confirm('آیا از انصراف  ماموریت اطمینان دارید؟')"><i
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
                <b>{{$mission_list->firstItem()}}</b>
                تا
                <b>{{$mission_list->lastItem()}}</b>
                از
                <b>{{$mission_list->total()}}</b>
                رکورد موجود
            </div>
            <br/>
            <div class="text-center">
                {{$mission_list->links('pagination::bootstrap-4')}}
            </div>
        </div>

    </div>

@endif
