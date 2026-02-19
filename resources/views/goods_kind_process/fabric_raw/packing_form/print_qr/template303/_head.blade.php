<html>
<head>
    @include("pdf._label_printer_head",["font_size"=>13])
    <style>


        .content table{
            text-align: right !important;
            border: 2px solid;
            border-collapse: separate; /* مهم برای گرد بودن */

            margin: 10px auto;
            border-radius: 15px;
        }
        .content td {
           padding: 5px !important;

            text-align: right !important;
        }
        .txt1{
            text-align: right;
            font-size: 21px;
            font-weight: bold;
        }
        .txt2{
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>

