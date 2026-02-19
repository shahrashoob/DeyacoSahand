@include("component.input._text",["id"=>"cron",'label'=>"کرون (Cron)","value"=>$script->cron,"class_col"=>"col-md-12"])

<div class="col-md-12">
    @include("component.input._aotocomplet2",[
        "id"=>"active_status_id",
        "label"=>" وضعیت ",
        "option"=>$status_option["items"],
        "val"=>$status_option["value"],
        "text"=>$status_option["text"],
        "class_col"=>""
        ])
</div>
@include("component.input._number",["id"=>"priority",'label'=>"اولویت اجرا","value"=>$script->priority,"class_col"=>"col-md-12"])
