<div class="row">
    <div class="col-sm-12">
        <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}" method="post"
              autocomplete="off"
              novalidate="novalidate">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    @include("component.input._select",[
                        "id"=>"product_planing_algorithm_id",
                        "label"=>" نوع الگوریتم برنامه ریزی ",
                        "option"=>$algorithm_option["items"],
                        "val"=>$product->product_planing_algorithm_id??"",
                        "text"=>$product->product_planing_algorithm->caption??"",
                        "class_col"=>""
                        ])
                </div>
            </div>
            <br/>
            <br/>
            @include($view_path."_btn_list")

        </form>
    </div>

</div>
<script>
</script>
