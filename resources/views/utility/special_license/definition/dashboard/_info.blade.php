<div class="row">
    <div class="col-md-12 ">
        <div class="alert alert-primary">
            لطفا لیست پست های سازمانی یا کمیته هایی که می بایست مجوز را تایید کند. در کادر زیر به ترتیب اولویت مشخص
            نمایید.
        </div>
    </div>
    @for($k=1;$k<=$max_priority;$k++)
        <div class="col-md-12">
            <h5>اولویت {{$k}}</h5>
        </div>
        <div class="col-md-9" data-select2-id="119">

            @include("component.input.select2._select2",[
           "id"=>"post_ids".$k,
           "label"=>" پست ها با اولویت ".$k,
           "option"=>$post_option[$k]["items"],
           "class_col"=>""
           ])
        </div>

        <div class="col-md-9" data-select2-id="119">

            @include("component.input.select2._select2",[
           "id"=>"committee_ids".$k,
           "label"=>"کمیته ها با اولویت ".$k,
           "option"=>$committee_option[$k]["items"],
           "class_col"=>""
           ])
        </div>
        <div class="col-md-9" data-select2-id="119">

            @include("component.input.select2._select2",[
           "id"=>"floating_post_ids".$k,
           "label"=>"پست های شناور با اولویت ".$k,
           "option"=>$floating_post_option[$k]["items"],
           "class_col"=>""
           ])
        </div>
    @endfor
</div>