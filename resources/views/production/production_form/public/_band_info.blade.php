<div class="col-xl-6 col-lg-12">
    <div class="card task-board-left">
        <div class="card-header">
            <h5> باند {{$production_form_item->band_code}} </h5>
        </div>
        <div class="card-block">

            <div class="row">

                @include("component.input._lable",["id"=>"","lable"=>"شماره ","value"=>$production_form_item->getCode(),"class_col"=>"col-md-12"])


                @include("component.input._lable",["id"=>"","lable"=>"کارت تولید  ","value"=>$production_form_item->production->serial,"url"=>route("production.dashboard.view_card",$production_form_item->production_id),"class_col"=>"col-md-12"])

                @include("component.input._lable_product",["id"=>"product".$production_form_item->id,"lable"=>"   کالا",
            "product_property"=>$production_form_item->production->product,
            "value"=>$production_form_item->production->product->code."-".$production_form_item->production->product->caption,"col"=>6])

                @if($item->version_code)
                    @include("component.input._lable",["id"=>"","lable"=>"ورژن ","value"=> "V".$item->version_code,"class_col"=>"col-md-12"])
                @endif

                <div class="col-md-12 offset-md-12">
                    <div class="form-group">
                        <label>لات (ها) :</label>
                        @foreach($production_form_item->lot_numbers as $item)

                            @include("line_product_station.product.lot_number._label",["lot_number"=>$item->lot_number,"amount"=>$item->amount,"id"=>$item->id,"product"=>$production_form_item->product])
                            ,
                        @endforeach
                        @foreach($production_form_item->lot_numbers as $item)
                            @include("line_product_station.product.lot_number._collapse",["lot_number"=>$item->lot_number,"id"=>$item->id])

                        @endforeach
                    </div>
                </div>


                @include("line_product_station.product.unit_of_measure_type._production",["label"=>$production_form_item->product->unit->measurement." سیستم ","production"=>$production_form_item->production,"units"=>["amount"=>$production_form_item->amount,"sub_amount"=>$production_form_item->sub_amount]])

                @if($setting["has_grading_and_control"]->integer_value)
                    @include("line_product_station.product.unit_of_measure_type._production",["label"=>$production_form_item->product->unit->measurement." کنترل کیفیت ","production"=>$production_form_item->production,"units"=>["amount"=>$production_form_item->amount_after_control]])

                @endif


                @include("line_product_station.product.unit_of_measure_type._production",["label"=>$production_form_item->product->unit->measurement." نهایی ","production"=>$production_form_item->production,"units"=>["amount"=>$production_form_item->final_amount,],"class_col"=>"col-md-12"])

                @if($production_form_item->product->sub_unit)
                    @include("component.input._lable",["id"=>"","lable"=>$production_form_item->product->sub_unit->measurement,"value"=>round($production_form_item->sub_amount,2)." کیلوگرم","class_col"=>"col-md-12"])
                @endif



                @include("component.input._lable",["id"=>"","lable"=>" درصد جمع شدگی اولیه ","value"=>$production_form_item->initial_shrinkage_percent])
                @include("component.input._lable",["id"=>"","lable"=>" درصد جمع شدگی ثانویه ","value"=>$production_form_item->second_shrinkage_percent])
                @include("component.input._lable",["id"=>"","lable"=>" درصد جمع شدگی کلی ","value"=>$production_form_item->general_shrinkage_percent])
                @php $packing_form_items=$production_form_item->getPackingFormItems();@endphp


                @if(count($packing_form_items) > 0)
                    <div class="col-md-12 offset-md-12">
                        <div class="form-group">
                            <label> شماره ردیف فرم بسته بندی :</label>
                            @foreach($packing_form_items as $packing_form_item)
                                <b><a href="{{route("fabric_raw.packing_form.view",$packing_form_item->packing_form_id)}}"
                                      target="_blank">{{$packing_form_item->code??""}}</a> </b> ,
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
            @if(isset($action_band_view_path))
                @include($action_band_view_path,["production_form_item"=>$production_form_item])
            @endif


        </div>
    </div>
</div>
