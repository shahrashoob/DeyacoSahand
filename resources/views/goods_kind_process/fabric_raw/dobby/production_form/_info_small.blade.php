<div class="row">


    @include("component.input._lable",["id"=>"","lable"=>" ماشین",
                "value"=>$production_form->machine->code." - ".$production_form->machine->caption,
                "url"=>route("fabric_raw.machine.dashboard.view",$production_form->machine_id)
                ])

    @include("component.input._lable",["id"=>"","lable"=>" کد غلطک پارچه","value"=>$production_form->carrier->code])

    @include("component.input._lable",["id"=>"","lable"=>" قطب های شروع","value"=>isset($production_form->start_machine_log)?$production_form->start_machine_log->getCounterTextList():""])
    @include("component.input._lable",["id"=>"","lable"=>" قطب های پایان","value"=>isset($production_form->end_of_machine_log)?$production_form->end_of_machine_log->getCounterTextList():""])
    @include("component.input._lable",["id"=>"","lable"=>" تعداد قطب","value"=>isset($production_form->start_machine_log) && isset($production_form->end_of_machine_log) ?
            $production_form->end_of_machine_log->sumCounter() - $production_form->start_machine_log->sumCounter():""    ])
    @include("component.input._lable",["id"=>"","lable"=>" وضعیت","value"=>$production_form->status->caption])


</div>


