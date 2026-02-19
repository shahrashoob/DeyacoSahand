<div class="col-md-12">
    <p class=" alert alert-warning">
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

@include("component.input._radio_box01",["id"=>"sent_address_place_type_of_transport",'label'=>"نوع آدرس محل ارسال بار توسط پیمانکار","label0"=>"محل مشتری","label1"=>"محل کارخانه","value"=>$contractor->sent_address_place_type_of_transport??""])

@include("component.input._number",["id"=>"duration_of_default_of_product",'mark'=>'*',"label"=>"مدت زمان پیش فرض  تحویل کالا توسط پیمانکار(روز)","value"=>$contractor->duration_of_default_of_product??""])

@include("component.input._number",["id"=>"minimum_time_required_to_start_coordination",'mark'=>'*',"label"=>"حداقل مدت زمان(ساعت) لازم جهت شروع هماهنگی","value"=>$contractor->minimum_time_required_to_start_coordination??""])
@include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
            "id"=>"end_date_of_contract",
            'label'=>"تاریخ پایان قرارداد ",
            "value"=>"",
            'mark'=>'*',
            ])

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