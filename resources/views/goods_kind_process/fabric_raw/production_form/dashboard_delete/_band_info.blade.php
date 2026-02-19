<div class="col-xl-6 col-lg-12">
    <div class="card task-board-left">
        <div class="card-header">
            <h5> باند {{$production_form_item->band_code}} </h5>
        </div>
        <div class="card-block">

            <div class="row">
                @include("component.input._lable",["id"=>"","lable"=>"شماره ","value"=>$production_form_item->getCode(),"class_col"=>"col-md-12"])
                @include("component.input._lable",["id"=>"","lable"=>"کارت تولید  ","value"=>$production_form_item->production->serial,"class_col"=>"col-md-12"])

                @include("component.input._lable_product",["id"=>"product".$production_form_item->id,"lable"=>"   پارچه خام",
            "product_property"=>$production_form_item->production->product,
            "value"=>$production_form_item->production->product->code."-".$production_form_item->production->product->caption,"col"=>6])


                <div class="col-md-12 offset-md-12">
                    <div class="form-group">
                        <label>همبافت های در حال بافت :</label>
                        @foreach($production_form_item->lot_numbers as $item)

                            @include("line_product_station.product.lot_number._label",["lot_number"=>$item->lot_number,"amount"=>$item->amount,"id"=>$item->id]),
                        @endforeach
                        @foreach($production_form_item->lot_numbers as $item)
                            @include("line_product_station.product.lot_number._collapse",["lot_number"=>$item->lot_number,"id"=>$item->id])

                        @endforeach
                    </div>
                </div>



                @include("component.input._lable",["id"=>"","lable"=>"متراژ سیستم ","value"=>$production_form_item->amount." ".$production_form_item->product->unit->caption??"","class_col"=>"col-md-12"])
                @include("component.input._lable",["id"=>"","lable"=>"متراژ کنترل کیفیت  ","value"=>$production_form_item->amount_after_control." ".$production_form_item->product->unit->caption??"","class_col"=>"col-md-12"])
                @include("component.input._lable",["id"=>"","lable"=>"متراژ نهایی  ","value"=>$production_form_item->final_amount." ".$production_form_item->product->unit->caption??"","class_col"=>"col-md-12"])

                @include("component.input._lable",["id"=>"","lable"=>"وزن ","value"=>$production_form_item->sub_amount." ".($production_form_item->product->sub_unit->caption??""),"class_col"=>"col-md-12"])
                @include("component.input._lable",["id"=>"","lable"=>" درصد جمع شدگی اولیه ","value"=>$production_form_item->initial_shrinkage_percent])
                @include("component.input._lable",["id"=>"","lable"=>" درصد جمع شدگی ثانویه ","value"=>$production_form_item->second_shrinkage_percent])
                @include("component.input._lable",["id"=>"","lable"=>" درصد جمع شدگی کلی ","value"=>$production_form_item->general_shrinkage_percent])

                @if(count($production_form_item->fabric_grading) > 0)
                   <div class="col-12" style="overflow: auto;">
                       <table class="table table-styling" style=" margin: auto; text-align: center">
                           <thead>
                           <tr>
                               <th>شماره ردیف</th>
                               <td>از متراژ</td>
                               <td>تا متراژ</td>
                               <td>درجه</td>
                               <td>همبافت</td>
                               <td>حامل</td>
                               <td>متراژ سیستم</td>
                               <td>متراژ کنترل کیفیت</td>
                               <td>متراژ نهایی</td>
                               <td>درصد جمع شدگی اولیه</td>
                               <td>درصد جمع شدگی ثانویه</td>
                               <td>درصد جمع شدگی نهایی</td>
                           </tr>

                           </thead>

                           <tbody>

                           @foreach($production_form_item->fabric_grading as $item)
                               <tr>
                                   <td>{{$item->getCode()}}</td>
                                   <td>{{$item->start_point}}</td>
                                   <td>
                                       {{$item->end_point}}
                                   </td>
                                   <td>
                                       {{$item->degree->caption??""}}
                                   </td>
                                   <td>
                                       {{$item->lot_number->code??""}}
                                   </td>
                                   <td>
                                       {{isset($item->packing_form_item->packing_form->carrier) ? $item->packing_form_item->packing_form->carrier->getCaption():""}}
                                   </td>
                                   <td>
                                       {{$item->amount}}
                                   </td>
                                   <td>
                                       {{$item->amount_after_control}}
                                   </td>
                                   <td>
                                       {{$item->final_amount??0}}
                                   </td>
                                   <td>
                                      {{$item->initial_shrinkage_percent}}
                                   </td>
                                   <td>
                                       {{$item->second_shrinkage_percent}}
                                   </td>
                                   <td>
                                       {{$item->final_shrinkage_percent}}
                                   </td>
                               </tr>
                           @endforeach


                           </tbody>
                       </table>
                   </div>

                @endif
            </div>

            @include("goods_kind_process.fabric_raw.production_form.dashboard._action_bands",["production_form_item"=>$production_form_item])

        </div>
    </div>
</div>
