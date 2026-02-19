<html>

<table style=" ">
    <thead>
    <tr>
        <th>نام</th>
        <th>نام خانوادگی</th>
        <th>تاریخ</th>
        <th>کل مدت حضور</th>
        <th>حضور غیر مجاز</th>
        <th>حضور مجاز</th>
        <th>ساعت کار قانونی</th>

        <th>صبح کار</th>
        <th>بعدازظهر کار</th>
        <th>شب کار</th>

        <th>مرخصی داخلی</th>
        <th> مرخصی استحقاقی</th>

        <th>غیبت داخلی</th>
        <th> غیبت قانونی</th>

        <th>اضافه کار</th>

        <th>ماموریت</th>

        <th> تعجیل مجاز در ورود</th>

        <th> تاخیر مجاز در ورود</th>

        <th> تعجیل مجاز در خروج</th>

        <th> تاخیر مجاز در خروج</th>

        <th>مرخصی استحقاقی داخلی</th>
        <th> مرخصی اضطراری</th>
        <th> مرخصی استعلاجی داخلی</th>
        <th> مرخصی ازدواج داخلی</th>
        <th> مرخصی فوت اقوام درجه یک داخلی</th>
        <th> مرخصی تشویقی داخلی</th>
        <th> مرخصی بدون حقوق داخلی</th>

    </tr>

    </thead>
    <tbody>
    @foreach($operation_list as $operation)
        <tr>

            <td>
                {{$operation->worker->firstname}}
            </td>
            <td>
                {{$operation->worker->lastname}}
            </td>
            <td>
                {{$operation->get_date("Y/m/d")}}
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
                {{$operation->get_time($operation->allowed_operation)}}
            </td>

            <td>{{$operation->get_time($operation->morning)}}</td>
            <td>{{$operation->get_time($operation->afternoon)}}</td>
            <td>{{$operation->get_time($operation->night)}}</td>

            <td>
                {{$operation->get_time($operation->leave)}}
            </td>

            <td>
                {{$operation->get_time($operation->legal_entitlement)}}
            </td>
            <td>
                {{$operation->get_time($operation->internal_absence)}}
            </td>
            <td>
                {{$operation->get_time($operation->legal_absence)}}
            </td>

            <td> {{$operation->get_time($operation->overtime)}}</td>


            <td>
                {{$operation->get_time($operation->mission)}}
            </td>


            <td>{{$operation->get_time($operation->allowed_earlier_time_for_entry)}}</td>

            <td>{{$operation->get_time($operation->allowed_delay_time_for_entry)}}</td>

            <td>{{$operation->get_time($operation->allowed_earlier_time_for_exit)}}</td>

            <td>{{$operation->get_time($operation->allowed_delay_time_for_exit)}}</td>


            <td>{{$operation->get_time($operation->leave_type_1)}}</td>
            <td>{{$operation->get_time($operation->leave_type_2)}}</td>
            <td>{{$operation->get_time($operation->leave_type_3)}}</td>
            <td>{{$operation->get_time($operation->leave_type_4)}}</td>
            <td>{{$operation->get_time($operation->leave_type_5)}}</td>
            <td>{{$operation->get_time($operation->leave_type_6)}}</td>
            <td>{{$operation->get_time($operation->leave_type_7)}}</td>


        </tr>

    @endforeach
    </tbody>

</table>
</html>
