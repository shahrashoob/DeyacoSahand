<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>"   تاریخ ابلاغ","value"=>$production->get_create_date()])

   @include("component.input._lable",["id"=>"","lable"=>"   ساعت ابلاغ ","value"=>$production->get_create_time()])


    @include("component.input._lable_product",["id"=>"product1".$production->id,"lable"=>"محصول",
             "product_property"=>$production->product,
             "value"=>$production->product->code."-".$production->product->caption])


    @include("component.input._lable",["id"=>"","lable"=>"   نوع کارت ","value"=>$production->production_type->caption??""])
    @include("component.input._lable",["id"=>"","lable"=>"   نوع کانال تولید ","value"=>$production->getProductionChannelType()?$production->getProductionChannelType()->caption:""])


    @include("line_product_station.product.unit_of_measure_type._production",["label"=>"   مقدار ","production"=>$production,"units"=>["amount"=>$production->number,"sub_amount"=>""]])

@if($production->number_of_packing_form)
        @include("component.input._lable",["id"=>"","lable"=>"تعداد بسته بندی","value"=>$production->number_of_packing_form." عدد "])
    @endif


    @if($production->normal_amount)
        @include("component.input._lable",["id"=>"","lable"=>"مقدار لوگو","value"=>$production->normal_amount." ".$production->product->unit->caption])
    @endif

    @include("line_product_station.product.unit_of_measure_type._machine_allocation",["label"=>"   مقدار تخصیص داده شده ","production"=>$production,"units"=>$production->get_allocation_amount(false,1,null,true,1000)])



    @include("line_product_station.product.unit_of_measure_type._production",["label"=>" مقدار تولید شده ","production"=>$production,"units"=>["amount"=>$production->get_production_amount() ]])

    @if($production->packing_type)
        @include("component.input._lable",["id"=>"","lable"=>"   نوع بسته بندی مجاز ","value"=>$production->packing_type->caption])
    @endif

    @include("component.input._lable",["id"=>"","lable"=>"   وضعیت ","value"=>$production->getStatus()])
    @include("component.input._lable",["id"=>"","lable"=>"   حداکثر تاریخ تحویل ","value"=>$production->get_max_delivery_date()])

    @include("component.input._lable",["id"=>"","lable"=>" شرح درخواست ","value"=>$production->order_list->description_request->text??""])

    <div class="col-md-6">
        <div class="form-group">
            <label>بسته بندی های مجاز:</label>
            @foreach($production->packing_types as $packing_type)
                <b>{{$packing_type->packing_type->caption}}</b>,
            @endforeach

        </div>
    </div>


@foreach($production->packing_types()->with("packing_type")->get() as $packing_type)
        {{$packing_type->caption}}
    @endforeach
</div>
