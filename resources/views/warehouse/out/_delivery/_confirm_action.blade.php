<div style="text-align: center">
    <a href="{{route("wh.out.dashboard.view",[$product_request_form])}}"
       class="btn btn-outline-dark">بازگشت</a>

    <input type="hidden" name="print" id="print" value="">

    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"> تایید نهایی و چاپ برگ خروج
    </button>
    <div class="dropdown-menu" style="text-align: center">
        <a class="dropdown-item" id="btn_confirm_print4"> تایید نهایی و چاپ برگ خروج (A4)</a>
        <a class="dropdown-item" id="btn_confirm_print3"> تایید نهایی و چاپ برگ خروج (A5)</a>
        <a class="dropdown-item" id="btn_confirm_print1"> تایید نهایی و چاپ برگ خروج
            (123*95)</a>

        <a class="dropdown-item" id="btn_confirm_back">تایید نهایی </a>


    </div>
    <br/>
    <br/>
    @include("component.input._select",[
                                       "id"=>"print_number",
                                       "class"=>"",
                                       "class_col"=>"",
                                       "style"=>"width:120px",
                                       "label"=>"تعداد پرینت ",
                                       "text_white"=>1,
                                       "option"=>$print_number_option["items"],
                                       "val"=>$print_number_option["value"],
                                       "text"=>$print_number_option["text"],
                                       ])
    @include("component.input._select",[
                                      "id"=>"print_type",
                                      "class"=>"",
                                      "class_col"=>"",
                                      "style"=>"width:118px",
                                      "label"=>"نوع چاپ برگ ",
                                      "text_white"=>1,
                                      "option"=>[
										  ["value"=>"product","caption"=>" به تفکیک کالا","selected"=>1] ,
										  ["value"=>"packing_form","caption"=>"به تفکیک بست بندی"] ,
                                            ["value"=>"product_and_packing_form","caption"=>" به تفکیک کالا و بست بندی"]
                                            ],
                                      "val"=>"product",
                                      "text"=>"چاپ برگ خروج (به تفکیک کالا)",
                                      ])




</div>
