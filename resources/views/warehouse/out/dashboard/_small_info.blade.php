
<div class="col-md-6">
    <div class="row">
        @include("component.input._lable",["label"=>"درخواست دهنده","value"=>$product_request_form->applicant->fullCaption()])
        @include("component.input._lable",["label"=>"وضعیت","value"=>$product_request_form->getStatus()])
        @include("component.input._lable",["label"=>"تاریخ و زمان ارسال کالا","value"=>$product_request_form->coordinate_date_time()])
        @if($product_request_form->allocation_id)
            @include("component.input._lable",["label"=>"شماره تخصیص","value"=>$product_request_form->allocation_id])

        @endif
        <div class="col-md-6 offset-md-6">
            <div class="form-group">
                <label>شماره مرجع ({{$product_request_form->applicant_type->reference_caption}}
                    ):</label>
                <b>{{$product_request_form->getReferenceNumber()}}</b>
            </div>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="row">
        @php $product_request_permissions=$product_request_form->get_product_request_permissions(); @endphp

        @if($product_request_permissions)
            @include("component.input._lable",["label"=>"روش ارسال بار","value"=>$product_request_permissions->shipping_method->caption??""])
            @include("component.input._lable",["label"=>"نوع خودرو","value"=>$product_request_permissions->car_type->caption??""])
            @include("component.input._lable",["label"=>" ارزش  بیمه نامه (ریال)","value"=>$product_request_permissions->insurance_amount])
            @include("component.input._lable",["label"=>"روش ارسال بار","value"=>$product_request_permissions->delivery_point_type->caption??""])
            @include("component.input._lable",["label"=>" هزینه ارسال بار (ریال)","value"=>$product_request_permissions->shipping_cost])

        @endif
    </div>
</div>




