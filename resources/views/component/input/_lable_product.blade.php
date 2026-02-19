<div class="{{ isset($product_property)?"col-md-12":(isset($class_col)?$class_col:"col-md-6 offset-md-6")}}">
    <div class="form-group">

        @php $lable=$lable??$label??""; @endphp
        @if($lable!="")
            <label>{{$lable}}:</label>
        @endif
        <a class=" collapsed" data-toggle="collapse" href="#{{$id??"collapseExample"}}" role="button"
           aria-expanded="false" aria-controls="{{$id??"collapseExample"}}">
            <b>{{isset($value)?$value:""}}</b>
        </a>
        @if(isset($image))
            &nbsp;&nbsp;&nbsp;
            <a href="{{asset("upload/product/".($product_property->image->filename??''))}}" class="view-image"
               data-title="{{isset($value)?$value:""}}">
                <b><i class="fa  fa-image"></i> تصویر کالا</b>
            </a>
        @endif

        @if(isset($product_consumed))
            &nbsp;&nbsp;&nbsp;
            <a class=" collapsed" data-toggle="collapse" href="#{{$id."consumed"}}" role="button"
               aria-expanded="false" aria-controls="{{$id."consumed"}}">
                <b>کالای مصرفی</b>
            </a>

        @endif

        @if(isset($basic_info))
            &nbsp;&nbsp;&nbsp;
            <a class=" collapsed" data-toggle="collapse" href="#{{$id."basic_info"}}" role="button"
               aria-expanded="false" aria-controls="{{$id."basic_info"}}">
                <b>اطلاعات تکمیلی</b>
            </a>
        @endif
        @include("line_product_station.goods_kind.property._property_value",["product"=>$product_property,"id"=>$id,"col"=>$col??"3"])
        @if(isset($product_consumed))
            @include("line_product_station.product.consumed_product._collapse_info",["product"=>$product_property,"id"=>$id."consumed"])

        @endif
        @if(isset($basic_info))
            @include("line_product_station.product._basic_info",["product"=>$product_property,"id"=>$id."basic_info"])

        @endif
    </div>
</div>
