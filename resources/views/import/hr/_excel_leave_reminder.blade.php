<html>
<table>
    <thead>
    <tr>
        <th>year</th>
        <th>national_code</th>
        <th>leave_type_id</th>
        <th>start_date_jalali</th>
        <th>end_date_jalali</th>
        <th>leave_remainder</th>
        <th></th>
    </tr>
    <tr>
        <th>سال مالی (مثال 1403)
            </th>
        <th>کد ملی
        </th>
        <th>نوع مرخصی (1 : استحقاقی)
        </th>
        <th>شروع سال مالی (تاریخ شمسی 1403/01/01)
        </th>
        <th>پایان سال مالی (تاریخ شمسی: 1403/12/30)
        </th>
        <th>مانده مرخصی (دقیقه)
        </th>
        <th>نام و نام خانوادگی</th>
    </tr>
    </thead>
    <tbody>
    @foreach($workers as $worker)
        <tr>
            <td>{{ $year }}</td>
            <td>{{ $worker->national_code }}</td>
            <td>1</td>

            <td>{{ $year }}/01/01</td>
            <td>{{ $year }}/12/29</td>

                <td>0</td>
            <td>
                {{$worker->fullname()}}
            </td>

        </tr>
    @endforeach
    </tbody>
</table>
</html>
