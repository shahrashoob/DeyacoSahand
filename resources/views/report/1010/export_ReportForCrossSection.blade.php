<html>

<table style=" ">
    <thead>
    <tr>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام ماشین</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار پیک تئوری</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">مقدار پیک عملی</th>

        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">مدت زمان(ثانیه)</td>
        <td style="text-align:center; border:2px solid #000000;background: #bfbfbf">شاخص بهره وری</td>


    </tr>

    </thead>
    <tbody>
    @foreach($list as $item)

        <tr>

            <td style="text-align:center; border:2px solid #000000;"> {{$item->caption??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$item->theory_contour??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{round($item->operation_contour??0)}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$item->time_in_seconds??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{$item->value??""}}</td>


        </tr>

    @endforeach
    </tbody>

</table>
</html>
