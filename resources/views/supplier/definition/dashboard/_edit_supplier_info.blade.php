<div class="col-md-12">
    <div class="row">

        <div class="w-100"></div>
        <div class="col-md-6">
            <img style="width: 200px"
                 src="{{asset("chatify_app/users-avatar/".($supplier->image->filename??''))}}"
                 onerror="this.onerror=null;this.src='{{url("assets/images/avatar.png")}}';"
            />
        </div>
        <br/>
        <div class="w-100"><br/></div>
        @if($allow_to_insert==0)
            <div class="col-md-6">
                @include("component.input._select",[
                    "id"=>"personal_type_id",
                    "label"=>" نوع شخصیت تامین کننده   ",
                    "option"=>$personal_type_option["items"],
                    "val"=>$personal_type_option["value"],
                    "text"=>$personal_type_option["text"],
                    "class_col"=>""
                    ])

                <div class="w-100"><br/></div>
                <div class="w-100" id="personal_type1" style="display: block">


                    @include("component.input._select",[
                                   "id"=>"user_id",
                                   "label"=>"نام تامین کننده ",
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
            @include("component.input._lable",["id"=>"personal_type_id","label"=>"نوع شخصیت تامین کننده","value"=>$supplier->personal_type->caption??""])
            @if($supplier->personal_type_id==1)
                @include("component.input._lable",["id"=>"user_id","label"=>"نام تامین کننده","value"=>$supplier->user->fullname()??""])
            @else
                @include("component.input._lable",["id"=>"company_id","label"=>" نام شرکت  ","value"=>$supplier->company->caption??""])
            @endif

        @endif
        <div class="w-100"><br/></div>
        @if($get_the_supplier_image)
            @include("component.input._file_upload", ["id"=>"user_image_file_id", 'label'=>"تصویر/لوگو","value"=> ""])
        @endif
        @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                    "id"=>"start_date_of_contract",
                    'label'=>"تاریخ شروع قرارداد ",
                    "value"=>$supplier->start_date_of_contract??"",
                    ])
        <br/>
        @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                    "id"=>"end_date_of_contract",
                    'label'=>"تاریخ پایان قرارداد ",
                    "value"=>$supplier->end_date_of_contract??"",
                    ])
        <div class="w-100"></div>
        <div class="col-md-12">
            @include("component.input._checkbox",["id"=>"can_i_borrow_from_this_supplier",'label'=>"آیا می توان از این تامین کننده قرض گرفت","checked"=>$supplier->can_i_borrow_from_this_supplier??0])
        </div>

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
            @include("component.input._text", ["id"=>"detailed_code", 'label'=>"کد تفضیلی",  "class_col"=>"","value"=>$supplier->detailed_code??""])

        </div>
        <div class="w-100"></div>
        <div class="col-md-6">
            @include("component.input._aotocomplet2",[
                "id"=>"supplier_type_id",
                "label"=>"نوع تامین کننده ",
                "option"=>$supplier_type_option["items"],
                "val"=>$supplier_type_option["value"],
                "text"=>$supplier_type_option["text"],
                "class_col"=>""
                ])
        </div>

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







