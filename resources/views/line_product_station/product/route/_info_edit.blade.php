
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5> ویرایش    {{$product_route->caption}} </h5>
            </div>
            <div class="card-block">

                <form id="form1" action="{{route($route_path."update",[$product,$product_route,$product_creation_process])}}" method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf
                    <div class="row">
                        @include("component.input._text",["id"=>"caption",'label'=>"عنوان مسییر ","value"=>$product_route->caption])

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

                    @include($view_path."_btn_back")

                    <button type="submit" class="btn btn-primary">  ذخیره تغییرات </button>

                </form>

            </div>
        </div>
    </div>

</div>
