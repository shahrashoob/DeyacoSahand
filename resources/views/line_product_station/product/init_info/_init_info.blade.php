
<div class="row">
    <div class="col-sm-12">
        <form id="form1" action="{{route("line_product_station.product.".($product->id?"update":"store"),$product)}}"
              method="post"
              autocomplete="off"
              novalidate="novalidate"
              enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    @include("component.input._select",[
                        "id"=>"product_service_type_id",
                        "label"=>" نوع (کالا/خدمت)  ",
                        "option"=>$product_service_type_option["items"],
                        "val"=>$product->product_service_type->id??"",
                        "text"=>$product->product_service_type->caption??"",
                        "class_col"=>""
                        ])
                </div>

            </div>
            <br/>
            <div id="product_service_type_1">
                @include("line_product_station.product.init_info._init_product_info")
            </div>
            <div id="product_service_type_2">
                @include("line_product_station.product.init_info._init_service_info")
            </div>

            <div class="" style="margin-top: 15px">
                <a href="{{route("line_product_station.product.index")}}" class="btn btn-outline-dark">بازگشت به داشبورد</a>


                <button type="submit" class="btn btn-primary"> ذخیره و ادامه</button>

                @if(isset($copy_from_other))
                    <a href="{{route("line_product_station.product.copy_from_other")}}" class="btn btn-primary">کپی کالا</a>

                @endif

                @if(isset($product->id))
                <a href="{{route("line_product_station.product.print",$product)}}" class="btn btn-outline-info">پرینت کارت کالا</a>
                @endif
            </div>

        </form>
    </div>
</div>

