@switch($product_route->supply_type_id)
    @case(1):
    @include("line_product_station.product.product_station._info_type1")
    @break

    @case(2)
    @include("line_product_station.product.product_station._info_type2")
    @break

    @case(3)
    @include("line_product_station.product.product_station._info_type3")
    @break

    @case(4)
    @include("line_product_station.product.product_station._info_type4")
    @break
@endswitch
