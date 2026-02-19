<div style="text-align: center">
    @if(isset($dashboard_type) && $dashboard_type=="customer")
        <a href="{{route("wh.out.customer.view",[$product_request_form->order->customer_id])}}?page={{$page}}"
           class="btn btn-outline-dark">بازگشت</a>
    @else
        <a href="{{route("wh.out.dashboard.view",[$product_request_form])}}"
           class="btn btn-outline-dark">بازگشت</a>
    @endif

    <input type="hidden" name="print" id="print" value="">

    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"> تایید نهایی و چاپ برگ خروج
    </button>
    @php $exit_form_label_printing_type_list=$product_request_form->warehouse->get_exit_form_label_printing_type(); @endphp

    <div class="dropdown-menu" style="text-align: center">
        @foreach($exit_form_label_printing_type_list as $exit_form_label_printing_type)
            <a class="dropdown-item btn_confirm_print" data-id="{{$exit_form_label_printing_type->id}}"> تایید نهایی و چاپ برگ
                خروج ({{$exit_form_label_printing_type->caption2}})</a>

        @endforeach
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
