<form id="form1" action="{{route("utility.setting.update")}}" method="post"
      novalidate="novalidate">
    @csrf
    <div class="row">


        @include("component.input._number",["id"=>$values["office_automation_max_file_size_in_mb"]->key,"lable"=>$values["office_automation_max_file_size_in_mb"]->caption,"value"=>$values["office_automation_max_file_size_in_mb"]->integer_value,"class_col"=>"col-md-6"])
        <div class="w-100"></div>
        <div class="col-md-6">
            <table class="table center">
                <tr>
                    <td colspan="5">ارسال پیامک</td>
                </tr>
                <tr>
                    <td></td>
                    <td>ایجاد کار</td>
                    <td>ثبت نتیجه انبار کار</td>
                    <td>تایید کار</td>
                    <td>ارجاع مجدد</td>
                </tr>
                @foreach($office_automation_priority as $item)
                    <tr>
                        <td>
                            {{$item->caption}}
                        </td>
                        <td>
                            <input type="checkbox" name="office_automation_priority_sms[{{$item->id}}][creatework]" {{isset($values["office_automation_priority_sms"]["value"][$item->id]["creatework"])?"checked":""}}>
                        </td>
                        <td>
                            <input type="checkbox" name="office_automation_priority_sms[{{$item->id}}][workdone]"   {{isset($values["office_automation_priority_sms"]["value"][$item->id]["workdone"])?"checked":""}}>
                        </td>
                        <td>
                            <input type="checkbox" name="office_automation_priority_sms[{{$item->id}}][confirm]"   {{isset($values["office_automation_priority_sms"]["value"][$item->id]["confirm"])?"checked":""}}>
                        </td>
                        <td>
                            <input type="checkbox" name="office_automation_priority_sms[{{$item->id}}][reject]"   {{isset($values["office_automation_priority_sms"]["value"][$item->id]["reject"])?"checked":""}}>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>


    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>
    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

</form>
