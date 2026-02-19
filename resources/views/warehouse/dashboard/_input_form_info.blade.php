<div class="row">
    @include("component.input._lable",["id"=>"","lable"=>"وضعیت فرم","value"=>$form->status->caption,"class_col"=>"col-md-4"])
    @include("component.input._lable",["id"=>"","lable"=>"کاربر ایجاد کننده","value"=>$form->worker->fullname(),"class_col"=>"col-md-4"])
    @include("component.input._lable",["id"=>"","lable"=>"انبار","value"=>$form->warehouse->caption,"class_col"=>"col-md-4"])
    @include("component.input._lable",["id"=>"","lable"=>"نوع رخداد","value"=>$form->trans_kind_item->caption,"class_col"=>"col-md-4"])
    @include("component.input._lable",["id"=>"","lable"=>"مرکز هزینه ","value"=>$form->ic,"class_col"=>"col-md-4"])
    @include("component.input._lable",["id"=>"","lable"=>"نام مرکز هزینه ","value"=>$form->cost_center_caption(),"class_col"=>"col-md-4"])
    @include("component.input._lable",["id"=>"","lable"=>"شماره مرجع","value"=>$form->referenceForInputForm(),"class_col"=>"col-md-4"])
</div>
