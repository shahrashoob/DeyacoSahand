<form id="form1" style="display: inline"
      action="{{route(!isset($worker)?"hr.worker.store":"hr.worker.update.info",$worker??"")}}" method="post"
      novalidate="novalidate"
      enctype="multipart/form-data">
    @csrf

    <div class="col-md-6">
        <div class="row">
            @include("component.input._text",["id"=>"firstname","label"=>"نام ","value"=>$worker->firstname??""])
            @include("component.input._text",["id"=>"lastname","label"=>"نام و نام خانوادگی ","value"=>$worker->lastname??""])
            @include("component.input._text",["id"=>"father_name","label"=>"نام پدر ","value"=>$worker->father_name??""])
            @include("component.input._text",["id"=>"national_code","label"=>"کد ملی ","value"=>$worker->national_code??""])
            @include("component.input._text",["id"=>"email","label"=>"نام کاربری  ","value"=>$worker->email??""])
            <div class="col-md-6">
                @include("component.input._aotocomplet2",[
                    "id"=>"country_id",
                    "label"=>" کشور   ",
                    "option"=>$country_option["items"],
                    "val"=>$country_option["value"],
                    "text"=>$country_option["text"],
                    "class_col"=>""
                    ])
            </div>
            <div class="w-100"></div>
            <div class="col-md-6">
                @include("component.input._aotocomplet2",[
                    "id"=>"cooperation_type_id",
                    "label"=>" نوع همکاری   ",
                    "option"=>$cooperation_type_option["items"],
                    "val"=>$cooperation_type_option["value"],
                    "text"=>$cooperation_type_option["text"],
                    "class_col"=>""
                    ])
            </div>

            <div class="w-100"></div>
            <div class="col-md-6">
                @include("component.input._aotocomplet2",[
                    "id"=>"one_time_token_status_id",
                    "label"=>" وضعیت کد پرسنلی یکبار مصرف",
                    "option"=>$one_time_token_status_option["items"],
                    "val"=>$one_time_token_status_option["value"],
                    "text"=>$one_time_token_status_option["text"],
                    "class_col"=>""
                    ])
            </div>
            <div class="w-100"></div>

            @include("component.input.datepicker._datepicker",["id"=>"end_date_of_contract","value"=>$worker->end_date_of_contract??null,"lable"=>"تاریخ قرارداد "])

            @include("component.input._text",["id"=>"mobile","label"=>"شماره همراه (بدون صفر)  ","value"=>$worker->mobile??""])
            @include("component.input._radio_box01",["id"=>"is_possible_to_work_remotely","label"=>" امکان دور کاری برای فرد وجود دارد","label0"=>"خیر","label1"=>"بله","value"=>$worker->is_possible_to_work_remotely??0])

            <div class="w-100"></div>
            <div class="col-md-6">
                <div class="row">
                    @include("component.input._file_upload",["id"=>"image_file","label"=>"تصویر کالا ( 200*200 پیکسل)","value"=>""])

                </div>
            </div>
            <div class="col-md-6">
                <img style="width: 300px" src="{{asset("upload/worker/".($worker->image->filename??''))}}"
                     onerror="this.onerror=null;this.src='{{url("upload/worker/worker.png")}}';"
                />

            </div>

        </div>
    </div>

    <div class="col-md-6">
        <div class="row">
            <h5> تغییر کلمه عبور</h5>
            <div class="col-md-6 offset-md-6">
                <div class="form-group">
                    <label>کلمه عبور جدید </label>
                    <input name="password" id="password" value="" type="password" class="form-control">
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
    <a href="{{route("hr.worker.index")}}" class="btn btn-outline-dark">بازگشت</a>

    <button type="submit" class="btn btn-primary"> ذخیره</button>

</form>


