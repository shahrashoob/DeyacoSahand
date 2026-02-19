@if($product_creation_process)
    <a href="{{route("line_product_station.product.product_creation.bom.index",[$product_creation_process,$bom->product_route->code])}}"
       class="btn btn-outline-dark">بازگشت</a>
@else
    <a href="{{route("line_product_station.product.bom.index",[$bom->product,$bom->product_route->code])}}"
       class="btn btn-outline-dark">بازگشت</a>
@endif