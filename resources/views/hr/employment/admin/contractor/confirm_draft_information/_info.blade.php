@include("component.input._lable",[
            "id"=>"start_of_work_time",
            "label"=>"ساعت شروع به کار انبار",
            "value"=>$employment->contractor->start_of_work_time??""
            ] )
@include("component.input._lable",[

            "id"=>"end_of_work_time",
            "label"=>"ساعت پایان کار انبار",
            "value"=>$employment->contractor->end_of_work_time??""
        ])
@include("component.input._lable",["id"=>"it_is_coordination_for_sending",'label'=>"آیا  پیمانکار هماهنگی دریافت مواد اولیه دارد؟","value"=>$employment->contractor->it_is_coordination_for_sending?"بله":"خیر"])
@include("component.input._lable",["id"=>"show_packing_forms_in_warehouse",'label'=>"آیا بسته بندی های موجود در انبار به پیمانکار نمایش داده شود؟","value"=>$employment->contractor->show_packing_forms_in_warehouse?"بله":"خیر"])
@include("component.input._lable",["id"=>"minimum_time_required_to_start_coordination","label"=>"حداقل مدت زمان(ساعت) لازم جهت شروع هماهنگی","value"=>$employment->contractor->minimum_time_required_to_start_coordination??""])
@include("component.input._lable",["id"=>"sent_address_place_type_of_transport",'label'=>"نوع آدرس محل ارسال بار توسط پیمانکار","value"=>$employment->contractor->sent_address_place_type_of_transport?"محل کارخانه":"محل مشتری"])
@include("component.input._lable",["id"=>"duration_of_default_of_product",'label'=>"مدت زمان پیش فرض  تحویل کالا توسط پیمانکار(روز)","value"=>$employment->contractor->duration_of_default_of_product??""])
@include("component.input._lable",["id"=>"is_order_registration_date_chosen_by_contractor","label"=>"آیا تاریخ ثبت سفارش توسط پیمانکار انتخاب شود","value"=>$employment->contractor->is_order_registration_date_chosen_by_contractor?"بله":"خیر"])
@include("component.input._lable",["id"=>"algorithm","label"=>"الگوریتم بارکد پیمانکار","value"=>$employment->contractor->barcode_algorithm->caption??"---"])

    @include("component.input._lable",[
        "id"=>"end_date_of_contract",
        "label"=>" پایان قرارداد ",
        "value"=>$employment->contractor->get_end_date_of_contract()??"",
        "class_col"=>""
        ])
