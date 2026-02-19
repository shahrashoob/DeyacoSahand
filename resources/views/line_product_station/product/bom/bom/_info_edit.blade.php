<form id="form1" action="{{route($route_path."update",[$bom,$product_creation_process])}}" method="post"
      autocomplete="off"
      novalidate="novalidate">
    @csrf
    <div class="row">
        @include("component.input._text",["id"=>"caption",'label'=>"عنوان BOM ","value"=>$bom->caption])

        <div class="col-md-6">
            @include("component.input._aotocomplet2",[
                "id"=>"active_status_id",
                "label"=>" وضعیت   ",
                "option"=>$status_option["items"],
                "val"=>$status_option["value"],
                "text"=>$status_option["text"],
                "class_col"=>""
                ])
        </div>
        <div class="w-100"></div>


    </div>

    @if($product_creation_process)
        <a href="{{route($route_path."index",[$product_creation_process,$bom->product_route->code])}}" class="btn btn-outline-dark">بازگشت</a>
    @else
        <a href="{{route($route_path."index",[$bom->product,$bom->product_route->code])}}" class="btn btn-outline-dark">بازگشت</a>

    @endif

    <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>

</form>
