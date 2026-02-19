@if(count($list)>0)
    <div class="row">

        <div class="col-sm-12">

            <div class="table-responsive">
                <table class="table table-styling" style="text-align: center!important;">
                    <thead>
                    <tr>
                        <td>ردیف</td>
                        <th>تاریخ و ساعت ورود</th>
                        <th>اقدام کننده</th>
                        <th>تاریخ و ساعت خروج</th>
                        <th>اقدام کننده</th>
                    </tr>

                    </thead>
                    <tbody>
                    @php $row=$list->firstItem();@endphp
                    @foreach($list as $item)
                        <tr>
                            <td title="{{$item->id}}">  {{$row++}}</td>
                            <td class='{{$item->entry_permit_status_id==461000100?"alert-danger":""}}'>
                                {{$item->entry_datetime(" Y/m/d - %A - H:i:s ")}}
                                @if($worker_id_equal_auth_id)
                                    <a href="{{route("utility.special_license.panel.new_special_license.index",[3,$item->id,0,0,"input"])}}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                {{$item->entry_register_worker?$item->entry_register_worker->fullname():""}}
                            </td>
                            <td class='{{$item->exit_permit_status_id==461000100?"alert-danger":""}}'>
                                {{$item->exit_datetime(" Y/m/d - %A - H:i:s ")}}
                                @if($worker_id_equal_auth_id && $item->exit_datetime)
                                    <a href="{{route("utility.special_license.panel.new_special_license.index",[3,$item->id,0,0,"output"])}}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                {{$item->exit_register_worker?$item->exit_register_worker->fullname():""}}
                            </td>
                        </tr>

                    @endforeach
                    </tbody>

                </table>
            </div>
            <div class="float-left">
                نمايش رکوردهای
                <b>{{$list->firstItem()}}</b>
                تا
                <b>{{$list->lastItem()}}</b>
                از
                <b>{{$list->total()}}</b>
                رکورد موجود
            </div>
            <br/>
            <div class="text-center">
                {{$list->links('pagination::bootstrap-4')}}
            </div>
        </div>


    </div>

@endif
