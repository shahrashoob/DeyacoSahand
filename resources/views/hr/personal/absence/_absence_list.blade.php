@if(count($absence_list)>0)
    <div class="row">

        <div class="col-sm-12">


            <div class="table-responsive">
                <table class="table table-styling" style="text-align: center!important;">
                    <thead>
                    <tr>
                        <td>ردیف</td>
                        <th>شماره درخواست</th>
                        <th>درخواست دهنده</th>
                        <th>زمان ثبت</th>
                        <th>زمان شروع غیبت</th>
                        <th>زمان پایان غیبت</th>
                        <th>جانشین</th>
                        <th>وضعیت</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$absence_list->firstItem();@endphp
                    @foreach($absence_list as $item)
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
                                {!! $item->get_replace_worker('<br/>') !!}
                            </td>
                            <td>

                                <a href="{{route("hr.personal.leave.log",$item)}}">
                                    {{$item->getStatus()}}
                                </a>


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
                <b>{{$absence_list->firstItem()}}</b>
                تا
                <b>{{$absence_list->lastItem()}}</b>
                از
                <b>{{$absence_list->total()}}</b>
                رکورد موجود
            </div>
            <br/>
            <div class="text-center">
                {{$absence_list->links('pagination::bootstrap-4')}}
            </div>
        </div>

    </div>

@endif
