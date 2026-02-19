
<html>
<head>
    @include("pdf._head",["font_size"=>14])
</head>
<body>

<div class="content">

        @include("accounting.client.factor._order_info",["caption"=>"فاکتور فروش"])

    <table>

        @include("accounting.client.factor._seller")

        @include("accounting.client.factor._buyer")

        @include("accounting.client.factor._col_text")

        @php
            $row=1;
        @endphp
            <tr>
                <td>{{$row++}}</td>
                <td>{{to_persian($client_factor->caption)}}</td>
                <td>1</td>
                <td>عدد</td>
                <td>{{number_format($client_factor->sum_amount)}}</td>
                <td>{{number_format($client_factor->sum_amount)}}</td>
                <td>0</td>
                <td>{{number_format($client_factor->tax)}}</td>
                <td>{{number_format($client_factor->total_amount)}}</td>
            </tr>
        <tr style="font-size: 16px; font-weight: bold;background: #e2d7d7">
            <td colspan="5"> جمع کل(ریال) :</td>
            <td colspan="4">{{number_format($client_factor->total_amount)??""}}</td>

        </tr>


    </table>


    @include("accounting.client.factor._footer_of_factor")
</div>

</body>
</html>
