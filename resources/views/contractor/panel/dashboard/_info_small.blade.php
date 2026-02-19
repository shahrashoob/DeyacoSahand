<div class="row">

    <div class="col-md-6">
        @include("component.input._lable",["id"=>"","lable"=>"سریال دستور پیمان","value"=>$contractor_allocation->production->serial()])
        @include("component.input._lable",["id"=>"","lable"=>"   تاریخ تخصیص","value"=>$contractor_allocation->get_create_date()])

        @include("component.input._lable",["id"=>"","lable"=>"   ساعت تخصیص ","value"=>$contractor_allocation->get_create_time()])



        @include("component.input._lable_product",["id"=>"product1".$contractor_allocation->id,"lable"=>"   کالا ",
            "product_property"=>$contractor_allocation->product,
            "value"=>$contractor_allocation->product->fullCaption()])

        @foreach ($bom->items as $item)
            @if(isset($product_bom[$item->material_id]))
                @include("component.input._label_bom",["id"=>"bom1","lable"=>" BOM","value"=>$item->material->caption,"bom"=> $product_bom[$item->material_id],"product"=>$item->material,"class_col"=>"col-md-12"])
            @endif
        @endforeach

        @include("component.input._lable",["id"=>"","lable"=>"   مقدار تخصیص  ","value"=>$contractor_allocation->allocation_amount." ".$contractor_allocation->product->unit->caption])
        @include("component.input._lable",["id"=>"","lable"=>"   مقدار تولید شده  ","value"=>$production_amount." ".$contractor_allocation->product->unit->caption])

        @php $packing_type_caption="";@endphp
        @foreach($contractor_allocation->production->packing_types as $pacing_type_production)
            @php $packing_type_caption.=$pacing_type_production->packing_type->caption.", ";@endphp
        @endforeach
        @include("component.input._lable",["id"=>"","lable"=>"   نوع بسته بندی مجاز ","value"=>trim($packing_type_caption??"***",", ")])


        @include("component.input._lable",["id"=>"","lable"=>"   وضعیت ","value"=>$contractor_allocation->status->caption])
    </div>


    <div class="col-md-6">
        <img style="width: 300px"
             src="{{asset("upload/product/".($contractor_allocation->product->image->filename??''))}}"
             onerror="this.onerror=null;this.src='{{url("upload/product/product.png")}}';"
        />
    </div>
</div>
