<html>
<head>
    @include("pdf._label_printer_head",["font_size"=>13])
    <style>
       .content th{
            text-align: center;
            border: none!important;
        }

       .content td{
            border: none !important;
           text-align: left;
           direction: ltr;
           padding: 4px;
           font-family: 'Times New Roman' !important;
        }
       .content table{
            border: none !important;
        }
    </style>
</head>
<body>

