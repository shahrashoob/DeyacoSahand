@php
    if(!function_exists("to_persian")){
        function to_persian($string) {
            $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

            $output= str_replace($english,$persian,  $string);
            return $output;
        }
    }

@endphp
<style>
    body {
        font-family: iransanse;
        font-size: {{isset($font_size)?$font_size:"12"}}px;
        font-weight: bold;
        color: #1b1e21;
        text-align: right;
        direction: rtl;
    }

    .content table {
        width: 100%;
    }

    .content table, .content th, .content td {
        border: 1px solid #1a1a1c;
        border-collapse: collapse;
        padding: 1px;
        text-align: center;
        vertical-align: middle;
    }

    .right {
        text-align: right !important;
    }

    .content table .title {
        font-weight: bold;
    }

    .content h3 {
        margin: 0;
        padding: 0;
    }

    .content hr {
        height: 1px;
        background: #1a1a1c;
        margin: 0px 0px 2px;
    }

    .center {
        text-align: center !important;
    }

    .border-none td, .border-none table {
        border: none !important;
    }

    .table_right td, th {
        text-align: right !important;
    }
</style>
