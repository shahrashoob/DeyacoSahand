<div class="col-xs-12">
    @include("component.input._textarea",["id"=>"comment","label"=>"توضیحات:"])

</div>
<br/>
@include("component.input._hidden",["id"=>'leave_overtime_id',"value"=>0,"class"=>"leaveId"])
@include("component.input._hidden",["id"=>'type',"value"=>0,"class"=>"leaveConformType"])
<div class="col-md-12">


    <input type="submit" class="btn btn-primary float-left " value="ثبت و ادامه"/>

    <a class="btn  md-close btn-outline-dark ">انصراف </a>
</div>


