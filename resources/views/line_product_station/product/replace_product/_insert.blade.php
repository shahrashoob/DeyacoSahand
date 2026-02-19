<form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}"
      method="post"
      autocomplete="off"
      novalidate="novalidate">
    @csrf
    <div class="col-sm-6">
        <div class="row">


            <div class="w-100"></div>
            @include("component.input._aotocomplet2",[
                "id"=>"replace_material_id",
                "label"=>" کالای جایگزین  مصرف ",
                "option"=>$product_option["items"],
                "val"=>"",
                "text"=>"",
                "class_col"=>"col-md-12"
                ])

        </div>

    </div>
    @include($view_path."_btn_list_2")
</form>
