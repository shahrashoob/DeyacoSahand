@php
    $contour_caption=$production_form->machine->machine_type->get_property_value(8);
    $contour_count=$production_form->machine->machine_type->get_property_value(6,1);
@endphp
<div class="{{ isset($product_property)?"col-md-12":(isset($class_col)?$class_col:"col-md-6 offset-md-6")}}">
    <div class="form-group">


        <label></label>
        <a href="#!" data-toggle="collapse" data-target="#{{$id??"collapseExample"}}"
           aria-expanded="false" aria-controls="{{$id??"collapseExample"}}">
            <b>{{$lable??$label??""}}</b>
        </a>

        <div class="collapse col-md-12" id="{{$id??"collapseExample"}}">


            <div class="alert " style="border: 1px solid #0b0b0b; border-radius: 10px">

                <div class="row">

                    @include("component.input._lable",["id"=>"","lable"=>"تعداد ".$contour_caption,"value"=>isset($production_form->start_machine_log) && isset($production_form->end_of_machine_log) ?
                            $production_form->end_of_machine_log->sumCounter() - $production_form->start_machine_log->sumCounter():""    ])


                    @if($production_form->start_machine_log_id)
                        @include("component.input._lable",["class_col"=>"col-md-12","id"=>"","lable"=>$contour_caption.($contour_count>1?" های":"")." شروع","value"=>isset($production_form->start_machine_log)?$production_form->start_machine_log->getCounterTextList():""])
                    @endif

                    @if($production_form->end_of_machine_log_id)
                        @include("component.input._lable",["class_col"=>"col-md-12","id"=>"","lable"=>$contour_caption.($contour_count>1?" های":"")."  پایان","value"=>isset($production_form->end_of_machine_log)?$production_form->end_of_machine_log->getCounterTextList():""])
                    @endif

                    @if($production_form->loading_machine_log_id)
                        @include("component.input._lable",["class_col"=>"col-md-12","id"=>"","lable"=>$contour_caption.($contour_count>1?" های":"")."  شروع در انتظار بارگذاری","value"=>isset($production_form->loading_machine_log)?$production_form->loading_machine_log->getCounterTextList():""])
                    @endif

                    @if($production_form->in_the_weaving_machine_log_id)
                        @include("component.input._lable",["class_col"=>"col-md-12","id"=>"","lable"=>$contour_caption.($contour_count>1?" های":"")."  شروع در حال بافت","value"=>isset($production_form->in_the_weaving_machine_log)?$production_form->in_the_weaving_machine_log->getCounterTextList():""])
                    @endif

                    @if($production_form->extraction_machine_log_id)
                        @include("component.input._lable",["class_col"=>"col-md-12","id"=>"","lable"=>$contour_caption.($contour_count>1?" های":"")."    استخراج پارچه","value"=>isset($production_form->extraction_machine_log)?$production_form->extraction_machine_log->getCounterTextList():""])
                    @endif

                    @if($production_form->in_the_finishing_weaving_machine_log_id)
                        @include("component.input._lable",["class_col"=>"col-md-12","id"=>"","lable"=>$contour_caption.($contour_count>1?" های":"")."  شروع در حال بافت پارچه یایانی","value"=>isset($production_form->in_the_finishing_weaving_machine_log)?$production_form->in_the_finishing_weaving_machine_log->getCounterTextList():""])
                    @endif


                    @if($production_form->fabric_raw_type_of_cut_for_create_form_id)
                    @include("component.input._lable",["class_col"=>"col-md-12","id"=>"","lable"=>" نوع استخراج پارچه در هنگام بارگذاری  ","value"=>$production_form->fabric_raw_type_of_cut_for_create_form->caption??""])
                    @endif

                        @if($production_form->fabric_raw_type_of_cut_for_extraction_form_id)
                    @include("component.input._lable",["class_col"=>"col-md-12","id"=>"","lable"=>" نوع استخراج پارچه در هنگام استخراج ","value"=>$production_form->fabric_raw_type_of_cut_for_extraction_form->caption??""])
                    @endif

                </div>
            </div>

        </div>


    </div>
</div>
