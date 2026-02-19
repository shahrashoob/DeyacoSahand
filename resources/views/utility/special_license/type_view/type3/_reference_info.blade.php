@if($param3=="input")
    @include("component.input._lable",["lable"=>"تاریخ و ساعت ورود","value"=>$reference->entry_datetime(" Y/m/d - %A - H:i:s ")])
@elseif($param3=="output")
    @include("component.input._lable",["label"=>"تاریخ و ساعت خروج","value"=>$reference->exit_datetime(" Y/m/d - %A - H:i:s ")])
@endif

@if($param3=="input")
    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"start_datetime","hasTime"=>1,"lable"=>" تاریخ و زمان صحیح ورود  ","class_col"=>"col-md-3","value"=>$reference->entry_datetime])
@elseif($param3=="output")
    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"end_datetime","hasTime"=>1,"lable"=>" تاریخ و زمان صحیح خروج  ","class_col"=>"col-md-3","value"=>$reference->exit_datetime])
@endif
<div class="w-100"></div>

