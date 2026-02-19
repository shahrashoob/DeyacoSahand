<div class="table-responsive">
    <table class="table table-styling center">

        <tbody>
        @php
            $row=1;
			$present_in_organ=0;
			$not_allowed_present_in_organ=0;
            $allowed_present_in_organ=0;
            $allowed_operation=0;
            $normal_operation=0;
            $morning=0;
            $afternoon=0;
            $night=0;
            $legal_leave=0;
            $internal_leave=0;
            $overtime=0;
            $mission=0;
            $internal_absence=0;
            $legal_absence=0;

            $allowed_earlier_time_for_entry=0;
            $allowed_delay_time_for_entry=0;
            $allowed_earlier_time_for_exit=0;
            $allowed_delay_time_for_exit=0;


        @endphp
        @foreach($operation_list as $operation)
            @php
                $present_in_organ+=$operation->present_in_organ;
                $not_allowed_present_in_organ+=$operation->not_allowed_present_in_organ;
                $allowed_present_in_organ+=$operation->allowed_present_in_organ;
                $allowed_operation+=$operation->allowed_operation;
                $normal_operation+=$operation->normal_operation;
                $morning+=$operation->morning;
                $afternoon+=$operation->afternoon;
                $night+=$operation->night;
                $legal_leave+=$operation->legal_leave;
                $internal_leave+=$operation->internal_leave;
                $overtime+=$operation->overtime;
                $mission+=$operation->mission;
            $internal_absence+=$operation->internal_absence;
            $legal_absence+=$operation->legal_absence;

                $allowed_earlier_time_for_entry+=$operation->allowed_earlier_time_for_entry;
                $allowed_delay_time_for_entry+=$operation->allowed_delay_time_for_entry;
                $allowed_earlier_time_for_exit+=$operation->allowed_earlier_time_for_exit;
                $allowed_delay_time_for_exit+=$operation->allowed_delay_time_for_exit;


            @endphp
        @endforeach

        <thead>
        <tr>
            <th>#</th>
            <th>روز هفته</th>
            <th>تاریخ</th>
            @if(isset($start_end_display))
                <th>تاریخ و ساعت ورود</th>
                <th>تاریخ و ساعت خروج</th>
            @endif
            <th>ساعت کار قانونی</th>
            <th>کل مدت حضور</th>
            <th>حضور غیر مجاز</th>
            <th>حضور مجاز</th>
            <th>کارکرد عادی</th>

            {{--            <th>صبح کار</th>--}}
            {{--            <th>بعدازظهر کار</th>--}}
            {{--            <th>شب کار</th>--}}

                <th>مرخصی قانونی</th>

            @if($internal_leave > 0)
                <th>مرخصی داخلی</th>
            @endif
            @if($overtime > 0)
                <th>اضافه کار</th>
            @endif
            @if($mission > 0)
                <th>ماموریت</th>
            @endif

                <th>غیبت داخلی</th>


                <th>غیبت قانونی</th>
                <th>غیبت کل</th>


                <th> تعجیل مجاز در ورود</th>

                <th> تاخیر مجاز در ورود</th>

                <th> تعجیل مجاز در خروج</th>

                <th> تاخیر مجاز در خروج</th>

        </tr>

        </thead>
        @foreach($operation_list as $operation)
            <tr>
                <td>{{$row++}}</td>

                <td  >
                    {{$operation->get_date("%A ")}}
                </td>
                <td  >
                    <a href="{{route("hr.personal.shift_work_day.show_entry_log_for_day",[$worker,$operation->id])}}">
                        <i class="fa fa-eye"></i>
                        {{$operation->get_date($format??"%d %B ")}}

                    </a>
                </td>
                @if(isset($start_end_display))
                    <td>
                        @foreach($operation_list_entry_log[$operation->id] as $log)
                            {{$log->entry_datetime(" m/d - H:i:s ")}}
                            <br/>
                        @endforeach
                    </td>
                    <td>
                        @foreach($operation_list_entry_log[$operation->id] as $log)
                            {{$log->exit_datetime(" m/d - H:i:s ")}}
                            <br/>
                        @endforeach
                    </td>
                @endif

                <td>
                    {{$operation->get_time($operation->allowed_operation)}}
                </td>
                <td>
                    {{$operation->get_time($operation->present_in_organ)}}
                </td>
                <td>
                    {{$operation->get_time($operation->not_allowed_present_in_organ)}}
                </td>
                <td>
                    {{$operation->get_time($operation->allowed_present_in_organ)}}
                </td>
                <td>
                    {{$operation->get_time($operation->normal_operation)}}
                </td>

                {{--                <td>{{$operation->get_time($operation->morning)}}</td>--}}
                {{--                <td>{{$operation->get_time($operation->afternoon)}}</td>--}}
                {{--                <td>{{$operation->get_time($operation->night)}}</td>--}}


                    <td>
                        {{$operation->get_time($operation->legal_leave)}}
                    </td>

                @if($internal_leave > 0)
                    <td>
                        {{$operation->get_time($operation->internal_leave)}}
                    </td>
                @endif
                @if($overtime > 0)
                    <td> {{$operation->get_time($operation->overtime)}}</td>

                @endif
                @if($mission > 0)
                    <td>
                        {{$operation->get_time($operation->mission)}}
                    </td>
                @endif

                    <td>  {{$operation->get_time($operation->internal_absence)}}</td>


                    <td>  {{$operation->get_time($operation->legal_absence)}}</td>
                    <td>  {{$operation->get_time($operation->legal_absence+$operation->internal_absence)}}</td>



                    <td>{{$operation->get_time($operation->allowed_earlier_time_for_entry)}}</td>


                    <td>{{$operation->get_time($operation->allowed_delay_time_for_entry)}}</td>

                    <td>{{$operation->get_time($operation->allowed_earlier_time_for_exit)}}</td>

                    <td>{{$operation->get_time($operation->allowed_delay_time_for_exit)}}</td>


            </tr>

        @endforeach

        @if(count($operation_list)>0)
            <tr>
                <th colspan="{{isset($start_end_display)?5:3}}">جمع کل</th>

                <th>
                    {{$operation->get_time($allowed_operation)}}
                </th>
                <th>
                    {{$operation->get_time($present_in_organ)}}
                </th>
                <th>
                    {{$operation->get_time($not_allowed_present_in_organ)}}
                </th>
                <th>
                    {{$operation->get_time($allowed_present_in_organ)}}
                </th>
                <th>
                    {{$operation->get_time($normal_operation)}}
                </th>


                {{--                <th>{{$operation->get_time($morning)}}</th>--}}
                {{--                <th>{{$operation->get_time($afternoon)}}</th>--}}
                {{--                <th>{{$operation->get_time($night)}}</th>--}}


                    <th>
                        {{$operation->get_time($legal_leave)}}
                    </th>

                @if($internal_leave > 0)
                    <th>
                        {{$operation->get_time($internal_leave)}}
                    </th>
                @endif
                @if($overtime > 0)
                    <th> {{$operation->get_time($overtime)}}</th>
                @endif
                @if($mission > 0)
                    <th>
                        {{$operation->get_time($mission)}}
                    </th>
                @endif

                    <th>  {{$operation->get_time($internal_absence)}}</th>


                    <th>  {{$operation->get_time($legal_absence)}}</th>
                    <th>  {{$operation->get_time($legal_absence+$internal_absence)}}</th>




                    <th>{{$operation->get_time($allowed_earlier_time_for_entry)}}</th>

                    <th>{{$operation->get_time($allowed_delay_time_for_entry)}}</th>

                    <th>{{$operation->get_time($allowed_earlier_time_for_exit)}}</th>

                    <th>{{$operation->get_time($allowed_delay_time_for_exit)}}</th>


            </tr>
        @else
            <tr>
                <td colspan="16">
                    هیچ ردیفی یافت نشد.
                </td>
            </tr>
        @endif
    </table>
</div>
