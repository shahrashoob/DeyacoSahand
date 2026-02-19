@php
    if(!function_exists("to_persian")){
        function to_persian($string) {
            $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

            $output= str_replace($english,$persian,  $string);
            return $output;
        }
			function to_money($number,$round=2) {
// تبدیل به عدد اعشاری مطمئن
            $number = floatval($number);

            // اگر عدد اعشار ندارد
            if (fmod($number, 1) == 0) {
                $formatted = number_format($number, 0, '٫', '٬');
            } else {
                // اگر اعشار دارد
                // حذف صفرهای اضافی انتهای اعشار
                $formatted = rtrim(rtrim(number_format($number, $round, '٫', '٬'), '۰'), '٫');
            }
            return to_persian($formatted);
        }
    }

@endphp
<style>
    body {
        font-family: iransanse;
        font-size: {{isset($font_size)?$font_size:"12"}}px;
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
        padding: 3px;
        text-align: center;
    }
    .content td {
        text-align: center;
        vertical-align: middle;
    }

    .right {
        text-align: right !important;
    }

    .content table .title {
        font-weight: bold;
        with: 130px
    }

    .content h3 {
        margin: 0;
        padding: 0;
    }

    .content hr {
        height: 1px;
        background: #1a1a1c;
        margin: 0px 0px 12px;
    }

    .center {
        text-align: center !important;
    }
    .border-none td,.border-none table{
        border: none !important;
    }

    .table_right td, th{
        text-align: right !important;
    }
</style>
