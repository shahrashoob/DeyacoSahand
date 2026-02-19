<table style="border: none">
    <tr>
        <td style="padding-right: 150px; border: none">
            <h2> {{$caption}} </h2>
        </td>
        <td style=" text-align: left !important;font-size: 12px;float: left; width: 150px; border: none">


            شماره :
            {{$client_factor->code??""}}
            -
            <br/>
            تاریخ :
            {{$client_factor->get_created_at()}}
        </td>
    </tr>
</table>
