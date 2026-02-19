<form id="form1" style="display: inline" action="" method="post" novalidate="novalidate">
    @csrf


    <div class="col-xs-12">
            @include("component.input._textarea",["id"=>"message","label"=>"توضیحات :"])
    </div>
    <br/>

    <div class="col-md-12">
            <input type="submit" class="btn btn-danger float-left " value="ثبت "/>
        <a class="btn  md-close btn-outline-dark ">انصراف </a>
    </div>

</form>