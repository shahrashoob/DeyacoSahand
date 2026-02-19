<div class="card">
    <div class="card-header ">
        <h5>ثبت دستور تولید مازاد</h5>
    </div>
    <div class="card-block ">
        <div class="alert alert-warning">
            در صورتی که نیاز است بیش از مقدار درخواست برای کالا کارت تولید ثبت شود، می توانید از طریق فرم زیر اقدام به ثبت کارت تولید نمایید.
            <br/>
            توجه: کارت تولید ثبت شده به این صورت بدون شناسه سفارش ثبت می گرد و قابل پیگیری در بخش سفارش ها نمی باشد.
        </div>
        <form id="form_over" autocomplete="off"
              action="{{route("sales.production_processing.submit_over_production",[$order_list])}}"
              method="post"
              novalidate="novalidate">
            @csrf
            <div class="row">
                @include("component.input._lable",[
                    "label"=>"  کالا ",
                    "value"=>$order_list->product->fullCaption(),

                    ])

                @include("component.input._lable",[
                    "label"=>"  نوع بسته بندی ",
                    "value"=>$order_list->product->default_packing_type->caption??"---",

                    ])
                <div class="w-100"></div>

                @include("component.input._select",[
                    "id"=>"over_priority_id",
                    "label"=>" اولویت ",
                    "option"=>$priority_option["items"],
                    "val"=>$priority_option["value"],
                    "text"=>$priority_option["text"],
                    "class_col"=>"col-md-4"
                    ])


                <div class="w-100"></div>
                @include("component.input.datepicker._datepicker",["id"=>"over_max_delivery_datetime1","lable"=>"حداکثر تاریخ تحویل  ","value"=>$order_list->order->delivery_datetime??null, "class_col"=>"col-md-4"])

                <div class="w-100"></div>

                @include("component.input._number",["id"=>"over_amount","class_col"=>"col-md-4",'label'=>"مقدار (واحد اصلی)  ","value"=>""])
                <div class="w-100"></div>
                @if($order_list->product->has_batch_with_packaging_number("exists"))
                    <div class="w-100"></div>
                    @include("component.input._number",["id"=>"over_number_of_packing_form","class_col"=>"col-md-4",'label'=>"تعداد بسته بندی  ","value"=>""])
                @endif
                <div class="col-md-12">
                    <a class="btn btn-outline-dark"
                       href="{{route("sales.dashboard.view_order",$order_list->order)}}">بارگشت</a>
                    <button type="submit" class="btn btn-primary"
                            onclick="return confirm('آیا مقدار کارت تولید  اطمینان دارید؟')"
                            id="btn_confirm">ثبت و تایید
                    </button>

                </div>
            </div>
        </form>
    </div>
</div>