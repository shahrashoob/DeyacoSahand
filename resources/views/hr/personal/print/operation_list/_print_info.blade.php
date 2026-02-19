<html>
<head>
    @include("pdf._label_printer_head",["font_size"=>9])
</head>
<body>
<div class="content">
    <div style="text-align: center"></div>

    <div style="text-align: center">
        {{$software_name}}
    </div>
    <div style="text-align: center">
        گزارش عمکرد {{$worker->fullname()}} در {{$caption}}
    </div>

    @include("hr.personal.shift_work_day._user_operation_list",["format"=>'m/d',"start_end_display"=>1])
    </div>


<div style="text-align: center; width: 100%;font-size: 11px">
    سازمان دیجیتال دیاکو
</div>

</body>
</html>
