
<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-styling">
            <thead>
            <tr>

                <th>  انتخاب ماشین آلات</th>
            </tr>

            </thead>
            <tbody>

            @foreach($machine_type_list as $item)
                <tr>

                    <td>
                        @php $access_level=$report_1005_line->get_access_level_machine_type($item->station_id,$item->id);@endphp
                        @switch($access_level)
                            @case("full")
                            <i class="fas fa-circle fa-lg text-success"></i>
                            @break
                            @case("empty")
                            <i class="fas fa-circle-notch fa-lg "></i>
                            @break
                            @default
                            <i class="fas fa-adjust fa-lg text-warning"></i>
                            @break
                        @endswitch

                        <b>{{$item->fullCaption()}}</b>


                    </td>

                    <td>
                        @if($access_level!="full")
                            <a href="{{route("report.1005.add_access",[0,0,$item,0])}}" onclick="return confirm('آیا از ثبت دسترسی اطمینان دارید؟')"
                               class="btn btn-outline-success btn-sm">  انتخاب همه </a>
                            <a href="{{route("report.1005.manage_access",[0,0,$item,0])}}"
                               class="btn btn-outline-primary btn-sm">مدیریت  انتخاب </a>
                        @endif
                        @if($access_level!="empty")
                            <a href="{{route("report.1005.remove_access",[0,0,$item,0])}}"
                               class="btn btn-outline-danger btn-sm"
                            >حذف کامل </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>

    </div>

</div>

