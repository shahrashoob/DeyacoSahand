<div class="row">

    @if($production_form->machine)
        @include("component.input._lable",["id"=>"","lable"=>" ماشین",
                    "value"=>$production_form->machine->code." - ".$production_form->machine->caption,
                    "url"=>route("production.machine.view",$production_form->machine_id)
                    ])

        @php $caption=$production_form->machine->machine_type->get_property_value(8);@endphp
        @if($caption)
            {{--        فقط برای پارچه خام مناسب است.--}}
            @include("goods_kind_process.fabric_raw.production_form._contour_info",["label"=>"مشاهده ".$caption." های فرم تولید"])
        @endif
    @else

    @endif


    @include("component.input._lable",["id"=>"","lable"=>" کد حامل ","value"=>$production_form->carrier->code??""])


    @include("component.input._lable",["id"=>"","lable"=>" وضعیت","value"=>$production_form->status->caption])


</div>


