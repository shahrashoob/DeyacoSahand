<div class="col-md-12">
    <div class="row">
        <div class="w-100"></div>
        <div class="col-md-6">
            <img style="width: 200px"
                 src="{{asset("chatify_app/users-avatar/".($contractor->image->filename??''))}}"
                 onerror="this.onerror=null;this.src='{{url("assets/images/avatar.png")}}';"
            />
        </div>
        <br/>
        <div class="w-100"><br/></div>
        @if($allow_to_insert==0)
            <div class="col-md-6">
                @include("component.input._select",[
                    "id"=>"personal_type_id",
                    "label"=>" نوع شخصیت پیمانکار   ",
                    "option"=>$personal_type_option["items"],
                    "val"=>$personal_type_option["value"],
                    "text"=>$personal_type_option["text"],
                    "class_col"=>""
                    ])

                <div class="w-100"><br/></div>
                <div class="w-100" id="personal_type1" style="display: block">


                    @include("component.input._select",[
                                   "id"=>"user_id",
                                   "label"=>"نام پیمانکار ",
                                   "option"=>$worker_option["items"],
                                   "val"=>$worker_option["value"],
                                   "text"=>$worker_option["text"],
                                   "class_col"=>""
                                   ])
                </div>

                <div class="w-100" id="personal_type2" style="display: none">

                    @include("component.input._select",[
                                "id"=>"company_id",
                                "label"=>" نام شرکت ",
                                "option"=>$company_option["items"],
                                "val"=>$company_option["value"],
                                "text"=>$company_option["text"],
                                "class_col"=>""
                                ])

                </div>
            </div>
        @else
            <div class="w-100"><br/></div>
            @include("component.input._lable",["id"=>"personal_type_id","label"=>"نوع شخصیت پیمانکار","value"=>$contractor->personal_type->caption??""])
            @if($contractor->personal_type_id==1)
                @include("component.input._lable",["id"=>"user_id","label"=>"نام پیمانکار","value"=>$contractor->worker->fullname()??""])
            @else
                @include("component.input._lable",["id"=>"company_id","label"=>" نام شرکت  ","value"=>$contractor->company->caption??""])
            @endif

        @endif
        <div class="w-100"><br/></div>
        @if($get_the_contractor_image)
            @include("component.input._file_upload", ["id"=>"user_image_file_id", 'label'=>"تصویر/لوگو","value"=> ""])
        @endif
        @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                "id"=>"start_date_of_contract",
                'label'=>"تاریخ شروع قرارداد ",
                "value"=>$contractor->start_date_of_contract??"",
                ])
        <br/>
        @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                    "id"=>"end_date_of_contract",
                    'label'=>"تاریخ پایان قرارداد ",
                    "value"=>$contractor->end_date_of_contract??"",
                    ])

        <div class="w-100"></div>
        <div class="col-md-6">
            @include("component.input._aotocomplet2",[
                "id"=>"cost_center_id",
                "label"=>"مرکز هزینه ",
                "option"=>$cost_center_option["items"],
                "val"=>$cost_center_option["value"],
                "text"=>$cost_center_option["text"],
                "class_col"=>""
                ])

        </div>
        <div class="w-100"></div>
        <div class="col-md-6">
            @include("component.input._select",[
                "id"=>"barcode_algorithm_id",
                "label"=>"الگوریتم بارکد پیمانکار",
                "option"=>$barcode_algorithm_option["items"],
                "val"=>$barcode_algorithm_option["value"],
                "text"=>$barcode_algorithm_option["text"],
                "class_col"=>""
                ])
<br/>
        </div>
        <div class="col-md-12">
        <p class="alert alert-warning">
             پیمان کار در ساعت های مشخص شده می توان جهت هماهنگی ارسال بار اقدام کند و خارج از این زمان ها امکان هماهنگی توسط پیمانکار وجود ندارد.

        </p>
        </div>
        @include("component.input._time",[
                    "id"=>"start_of_work_time",
                    "label"=>"ساعت شروع به کار انبار",
                    "m"=>isset($contractor->start_of_work_time)?explode(":",$contractor->start_of_work_time)[1]:"0",
                    "h"=>isset($contractor->start_of_work_time)?explode(":",$contractor->start_of_work_time)[0]:""
                    ] )

        @include("component.input._time",[

	                "id"=>"end_of_work_time",
                    "label"=>"ساعت پایان کار انبار",
                    "m"=>isset($contractor->end_of_work_time)?explode(":",$contractor->end_of_work_time)[1]:"0",
                    "h"=>isset($contractor->end_of_work_time)?explode(":",$contractor->end_of_work_time)[0]:""
                ])


        <div class="w-100"></div>
        <div class="col-md-12">
            @include("component.input._checkbox",["id"=>"it_is_coordination_for_sending",'label'=>"آیا  پیمانکار هماهنگی دریافت مواد اولیه دارد؟","checked"=>$contractor->it_is_coordination_for_sending??0])
        </div>

        <div class="w-100"></div>
        <div class="col-md-12">
            @include("component.input._checkbox",["id"=>"show_packing_forms_in_warehouse",'label'=>"آیا بسته بندی های موجود در انبار به پیمانکار نمایش داده شود؟","checked"=>$contractor->show_packing_forms_in_warehouse??0])
        </div>
        @include("component.input._radio_box01",["id"=>"sent_address_place_type_of_transport",'label'=>"نوع آدرس محل ارسال بار توسط پیمانکار","label0"=>"محل مشتری","label1"=>"محل کارخانه","value"=>$contractor->sent_address_place_type_of_transport])

{{--        @include("component.input._radio_box01",["id"=>"is_order_registration_date_chosen_by_contractor",'label'=>"آیا تاریخ ثبت سفارش توسط پیمانکار انتخاب شود","label0"=>"خیر","label1"=>"بله","value"=>$contractor->is_order_registration_date_chosen_by_contractor])--}}
        @include("component.input._number",["id"=>"duration_of_default_of_product","label"=>"مدت زمان پیش فرض  تحویل کالا توسط پیمانکار(روز)","value"=>$contractor->duration_of_default_of_product??""])

        @include("component.input._number",["id"=>"minimum_time_required_to_start_coordination","label"=>"حداقل مدت زمان(ساعت) لازم جهت شروع هماهنگی","value"=>$contractor->minimum_time_required_to_start_coordination??""])


        <div class="w-100"></div>
        <div class="col-md-6">
            @include("component.input._aotocomplet2",[
                "id"=>"active_status_id",
                "label"=>"وضعیت ",
                "option"=>$status_option["items"],
                "val"=>$status_option["value"],
                "text"=>$status_option["text"],
                "class_col"=>""
                ])
        </div>
        <div class="w-100"></div>


    </div>
</div>







