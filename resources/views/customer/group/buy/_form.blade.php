
<form id="form1" style="display: inline" action="" method="post" novalidate="novalidate">
    @csrf


    <div class="col-xs-12">
        @if($type==1)

            @include("component.input._textarea",["id"=>"comment","label"=>"توضیحات:"])

        @else
            @include("component.input._textarea",["id"=>"message","label"=>"لطفا دلیل عدم تایید را وارد نمایید."])

        @endif
</div>
<br/>
<div class="col-md-12">

    @if($type==1)
        <input type="submit" class="btn btn-primary float-left " value="تایید نهایی"/>

    @else
        <input type="submit" class="btn btn-danger float-left " value="ثبت و ادامه"/>

    @endif
    <a class="btn  md-close btn-outline-dark ">انصراف </a>
</div>

</form>

<script>

</script>
