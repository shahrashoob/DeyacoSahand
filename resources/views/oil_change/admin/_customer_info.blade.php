<div class="row">
    @include("component.input._text",["id"=>"shop_name",'label'=>"نام مغازه ","value"=>$customer->shop_name ??"","autofocus"=>1])
    @include("component.input._text",["id"=>"firstname",'label'=>"نام  ","value"=>$customer->firstname ??"","autofocus"=>1])
    @include("component.input._text",["id"=>"lastname",'label'=>"نام خانوادگی ","value"=>$customer->lastname ??""])
    @include("component.input._number",["id"=>"national_code",'label'=>"کد ملی ","value"=>$customer->national_code ??""])
    @include("component.input._number",["id"=>"mobile",'label'=>"موبایل  ","value"=>$customer->mobile ??""])

    @include("component.input._text",["id"=>"email","label"=>"نام کاربری  ","value"=>$customer->user->email ??""])
    @include("component.input._text",["id"=>"max_days_to_send_driver","label"=>"حداکثر زمان ارسال پیامک جهت تعویض روغن (روز)","value"=>$customer->max_days_to_send_driver ??""])
    <div class="col-md-12">
    @include("component.input._checkbox",["id"=>"calculator_option","label"=>"ماژول ماشین حساب","checked"=>$customer->calculator_option ??null])
    @include("component.input._checkbox",["id"=>"sms_to_driver_option","label"=>"ارسال پیامک به مشتری جهت تعویض روغنی","checked"=>$customer->sms_to_driver_option ??null])

    </div>
    <div class="col-md-6">
        <div class="row">
            <h5> تغییر/ثبت کلمه عبور</h5>
            <div class="col-md-6 offset-md-6">
                <div class="form-group">
                    <label>کلمه عبور جدید </label>
                    <input name="password" autocomplete="false" id="password" value="" type="password" class="form-control">
                </div>
            </div>
            <div class="col-md-6 offset-md-6">
                <div class="form-group">
                    <label>تکرار کلمه عبور </label>
                    <input name="confirm_password" id="confirm_password" value="" type="password" class="form-control">
                </div>
            </div>
        </div>


    </div>

</div>
