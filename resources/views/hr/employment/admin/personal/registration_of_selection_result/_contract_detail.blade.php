<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5> مشخصات قرارداد </h5>
            </div>
            <div class="card-block">
                <p>حق شاغل
                    عبارتست از کلیه دریافتی های مرتبط به شاغل به واسطه شاغل بودن در سازمان و تفاوتی بین کارگران و
                    کارمندان در دریافت آن وجود ندارد(مزایای رفاهی و انگیزشی).</p>
                <div class="row">

                    @include("component.input._select", [
                            "id"=>"absorption_type_id",
                            "label"=>"نوع جذب",
                            "option"=>$absorption_type_option["items"],
                            "val"=>$absorption_type_option["value"],
                            "text"=>$absorption_type_option["text"],
                            "class_col"=>"col-md-3",
                            'mark'=>'*'
                            ])
                    <div class="w-100"></div>
                    <br>
                    @include("component.input._number",["id"=>"right_to_work",'label'=>"حق شاغل(ریال)","value"=>"", "class_col"=>"col-md-3",'mark'=>'*'])
                    <div class="w-100"></div>
                    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                                        "id"=>"start_date_of_contract" ,
                                         "label"=>"تاریخ شروع قرارداد",
                                         "class"=>"form-control",
                                         "class_col"=>"col-md-3",
                                          "name"=>"coordination_time" ,
                                          'mark'=>'*'
                                          ])
                    <div class="w-100"></div>
                    @include("component.input.datepicker.jalali_datepicker._jalali_datepicker", [
                                                              "id"=>"end_date_of_contract" ,
                                                               "label"=>"تاریخ پایان قرارداد",
                                                               "class"=>"form-control",
                                                               "class_col"=>"col-md-3",
                                                                "name"=>"coordination_time" ,
                                                                'mark'=>'*'
                                                                ])


                </div>
                <div class="w-100"><br/></div>
            </div>
        </div>
    </div>

</div>



