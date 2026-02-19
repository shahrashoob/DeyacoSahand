<div class="row">
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>
               دریافت حساب اینترنت
            </h5>

        </div>
        <div class="card-block">
            @include("component.input._text", ["id"=>"internet_account_username", 'label'=>"نام کاربری",  "class_col"=>"col-md-3",'mark'=>'*'])
            @include("component.input._password", ["id"=>"password", 'label'=>"پسورد",  "class_col"=>"col-md-3",'mark'=>'*'])

            <div class="col-md-3">
            <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}" class="btn btn-outline-dark">بازگشت</a>

            <button type="submit" class="btn btn-primary">ثبت</button>
        </div>
        </div>

    </div>

</div>

</div>