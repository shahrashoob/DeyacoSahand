<html>

<table style=" ">
    <thead>
    <tr>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">کد بسته بندی</th>
        <th style="text-align:center; border:2px solid #000000;background: #bfbfbf">نام درخواست دهنده</th>

    </tr>

    </thead>
    <tbody>
    @foreach($packing_list as $item)

        <tr>

            <td style="text-align:center; border:2px solid #000000;"> {{$item->code??""}}</td>
            <td style="text-align:center; border:2px solid #000000;"> {{isset($form_packing_form[$item->id]) && isset( $applicant_name[$form_packing_form[$item->id]])?$applicant_name[$form_packing_form[$item->id]]:""}} </td>


        </tr>

    @endforeach
    </tbody>

</table>
</html>
