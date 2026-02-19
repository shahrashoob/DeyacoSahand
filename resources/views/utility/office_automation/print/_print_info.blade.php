<html>
<head>
    @include("pdf._label_printer_head",["font_size"=>13])
</head>
<body>
<div class="content">
    <table style="width: 100%">
        <tr>
            <td colspan="6">
                {{$software_name}}

                <br/>
                اتوماسیون اداری - میز کار
                {{$office_automation_work->code}}
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: right">
                عنوان: {{$office_automation_work->caption}}
                <br/>
                ایجاد کننده:  {{$office_automation_work->worker->fullName()}}
                <br/>
                تاریخ ایجاد: {{$office_automation_work->get_create_date_and_time()}}
                <br/>
                تاریخ پایان: {{$office_automation_work->end_datetime()}}
                <br/>
                اولویت:
                {!! $office_automation_work->priority->getHtml() !!}
                <br/>
            </td>
        </tr>


    </table>
</div>
<div style="text-align: center; width: 100%;font-size: 9px">
    سازمان دیجیتال دیاکو
</div>

</body>
</html>
