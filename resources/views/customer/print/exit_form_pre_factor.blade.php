
<html>
<head>
    @include("pdf._head",["font_size"=>14])
</head>
<body>

<div class="content">

    @include("customer.print._order_info",["caption"=>"برگ خروج ریالی","form"=>$form])

    <table>

        @include("customer.print._seller")

        @include("customer.print._buyer")

        @include("customer.print._col_text")

        @include("customer.print._exit_form_factor_table")


    </table>



</div>

</body>
</html>
