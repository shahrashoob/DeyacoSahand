<div class="row">
    <div class="col-md-12">
        <h4>{{$product->caption}}</h4>
    </div>
    <br/>
    <div class="w-100"></div>

    @include("component.input._lable",["id"=>"amount",'label'=>"مقدار اصلی (".$product->unit->caption.")","value"=>$form_item->amount,"autofocus"=>1])
    @if(isset($product->sub_unit))
        @include("component.input._lable",["id"=>"sub_amount",'label'=>"مقدار فرعی (".$product->sub_unit->caption.")","value"=>$form_item->sub_amount??""])
    @endif

    @include("component.input._lable",["id"=>"lot_number",'label'=>"شماره همبافت (شید | لات)","value"=>$form_item->lot_number->code??""])
    @include("component.input._lable",["id"=>"degree",'label'=>"درجه کالا","value"=>$form_item->degree->caption??""])




</div>
