@if($product_creation_process)

    <a href="{{route("line_product_station.product.product_creation.route.index",$product_creation_process)}}"
       class="btn btn-outline-dark">بازگشت
    </a>

@else
    <a href="{{route("line_product_station.product.index")}}" class="btn btn-outline-dark">بازگشت
        به داشبورد</a>

    <a href="{{route("line_product_station.product.route.index",$product)}}"
       class="btn btn-outline-dark">بازگشت
    </a>

@endif



<button type="submit" class="btn btn-primary"> ذخیره</button>
