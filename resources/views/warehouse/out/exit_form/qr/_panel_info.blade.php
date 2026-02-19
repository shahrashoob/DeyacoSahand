<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5> برگ خروج از انبار: {{$form->getCode()}} </h5>
        </div>

        <div class="card-block">
            <div class="row">
                <div class="col-md-6">
                    @include("component.input._lable",["lable"=>"شماره درخواست(ها)","value"=>$form->getAllProductRequestFormCodes()])

                    @if($product_request_form_form)
                        @include("component.input._lable",["lable"=>"درخواست دهنده","value"=>$product_request_form_form->product_request_form->applicant->fullCaption()])
                        @include("component.input._lable",["lable"=>"شماره سفارش","value"=>$product_request_form_form->product_request_form->order?$product_request_form_form->product_request_form->order->code():""])
                    @endif

                    @include("component.input._lable",["lable"=>"تاریخ","value"=>$form->get_create_date_and_time()])
                    @include("component.input._lable",["lable"=>"وضعیت","value"=>$form->status->caption])
                    @include("component.input._lable",["lable"=>"انبار","value"=>$form->warehouse->caption])


                </div>
                <div class="col-md-6">
                    @include("component.input._lable",["lable"=>"تعداد بسته بندی","value"=>$form->getPackingFromCount()." عدد "])
                    @include("component.input._lable",["lable"=>"تعداد بسته بندی های حمل و نقل","value"=>$form->getTransportCount()." عدد "])

                    @include("component.input._lable",["lable"=>$unit->measurement." کل","value"=>$sum_amount."  ".$unit->caption])
                    @if($sub_unit)
                        @include("component.input._lable",["lable"=>$sub_unit->measurement." کل","value"=>$sum_sub_amount." ".$sub_unit->caption])
                    @endif
                    @include("component.input._lable",["id"=>"","lable"=>"مرکز هزینه ","value"=>$form->ic,"class_col"=>"col-md-4"])
                    @include("component.input._lable",["id"=>"","lable"=>"نوع رخداد","value"=>$form->trans_kind_item->caption,"class_col"=>"col-md-4"])


                </div>
            </div>



        </div>
    </div>
</div>
