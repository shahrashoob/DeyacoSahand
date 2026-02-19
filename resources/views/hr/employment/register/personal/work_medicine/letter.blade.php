<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <title>نامه طب کار</title>

</head>
<body style=" font-family: 'iransanse'; font-size: 10pt; text-align:justify;width: 100%;height: 100%;">
<p style=" direction: rtl;text-align: center; align-items: center;">بسمه تعالی</p>
<h3 style="direction: rtl;">{{$name_of_work_medicine_doctor_setting}}</h3>
<h3 style="direction: rtl;">با سلام</h3>
<p style="direction: rtl;">احتراما، بدین وسیله
    <b>{{$employment->worker->fullname()}}</b>
    فرزند
    <b>{{$employment->worker->father_name}}</b>
    به سمت
    <b>{{$employment->post->caption}}</b>
    جهت انجام معاینات بدو استخدام معرفی می گردد. خواهشمند است اقدامات لازم مبذول فرمایید.
    مزید تشکر و امتنان فراوان خواهد بود.
</p>
<table>
    <tr>
        <td></td>
    </tr>
    <tr>
        <th>با تشکر</th>
    </tr>
    <tr>
        <th>{{$post_user->post->caption}}</th>
    </tr>
    <tr>
        <th>{{$post_user->worker->fullname()}}</th>
    </tr>
</table>

</body>
</html>
