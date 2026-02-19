
<div class="alert alert-info w-100">
    لطفا کد بسته بندی (بدون DCPK) که قصد دارید به جای کالای (
    {{$object1->product->fullCaption()}}
    ) از انبار خارج شود را وارد نمایید.

</div>
@include("component.input._number",["id"=>"packing_code","label"=>"کد بسته بندی مورد نظر (بدون DCPK)","value"=>"","class_col"=>"col-md-3"])

<div class="w-100"></div>
