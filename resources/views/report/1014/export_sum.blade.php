<html>

<table style=" ">
    <thead>
    <tr>
        <th style="background-color: #3f88fb">نام</th>
        <th style="background-color: #3f88fb">نام خانوادگی</th>
        <th style="background-color: #3f88fb">کل مدت حضور</th>
        <th style="background-color: #3f88fb">حضور غیر مجاز</th>
        <th>حضور مجاز</th>
        <th>کارکرد عادی </th>
        <th style="background-color: #3f88fb">ساعت کار قانونی</th>

        <th>صبح کار</th>
        <th>بعدازظهر کار</th>
        <th>شب کار</th>

        <th style="background-color: #D9D9D9">مرخصی قانونی</th>

        <th style="background-color: #D9D9D9">غیبت داخلی</th>
        <th style="background-color: #D9D9D9"> غیبت قانونی</th>
        <th style="background-color: #3f88fb"> غیبت کل</th>

        <th style="background-color: #D9D9D9">اضافه کار</th>

        <th style="background-color: #D9D9D9">ماموریت</th>
        <th style="background-color: #D9D9D9">کسر اضافه کار از غیبت (داخلی/قانونی)</th>

        <th> تعجیل مجاز در ورود</th>

        <th> تاخیر مجاز در ورود</th>

        <th> تعجیل مجاز در خروج</th>

        <th> تاخیر مجاز در خروج</th>

{{--        <th>مرخصی استحقاقی داخلی</th>--}}
{{--        <th> مرخصی اضطراری</th>--}}
{{--        <th> مرخصی استعلاجی داخلی</th>--}}
{{--        <th> مرخصی ازدواج داخلی</th>--}}
{{--        <th> مرخصی فوت اقوام درجه یک داخلی</th>--}}
{{--        <th> مرخصی تشویقی داخلی</th>--}}
{{--        <th> مرخصی بدون حقوق داخلی</th>--}}

    </tr>

    </thead>
    <tbody>
    @foreach($operation_list as $operation)
        <tr>

            <td style="background-color: #D9D9D9">
                {{$operation->worker->firstname}}
            </td>
            <td style="background-color: #D9D9D9">
                {{$operation->worker->lastname}}
            </td>
            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->present_in_organ)}}
            </td>
            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->not_allowed_present_in_organ)}}
            </td>
            <td >
                {{$operation->get_time($operation->allowed_present_in_organ)}}
            </td>
            <td >
                {{$operation->get_time($operation->normal_operation)}}
            </td>
            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->allowed_operation)}}
            </td>

            <td>{{$operation->get_time($operation->morning)}}</td>
            <td>{{$operation->get_time($operation->afternoon)}}</td>
            <td>{{$operation->get_time($operation->night)}}</td>


            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->legal_leave)}}
            </td>
            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->internal_absence)}}
            </td>
            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->legal_absence)}}
            </td>

            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->legal_absence+$operation->internal_absence)}}
            </td>

            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->overtime)}}
            </td>


            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->mission)}}
            </td>

            <td style="background-color: #D9D9D9">
                {{$operation->get_time($operation->overtime-$operation->internal_absence-$operation->legal_absence)}}
            </td>


            <td>{{$operation->get_time($operation->allowed_earlier_time_for_entry)}}</td>

            <td>{{$operation->get_time($operation->allowed_delay_time_for_entry)}}</td>

            <td>{{$operation->get_time($operation->allowed_earlier_time_for_exit)}}</td>

            <td>{{$operation->get_time($operation->allowed_delay_time_for_exit)}}</td>




        </tr>

    @endforeach
    </tbody>

</table>
</html>
