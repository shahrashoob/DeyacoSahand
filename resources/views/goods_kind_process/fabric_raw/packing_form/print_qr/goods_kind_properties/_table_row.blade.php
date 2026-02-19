@php
    $product=$packing_form->items->first()->product;
    $goods_kind_id_for_properties=$product->goods_kind_id;@endphp
@switch($goods_kind_id_for_properties)
    @case(4)
        @include("goods_kind_process.fabric_raw.packing_form.print_qr.goods_kind_properties.4._table_row",["product"=>$product])

        @break

    @case(5)
        @include("goods_kind_process.fabric_raw.packing_form.print_qr.goods_kind_properties.5._table_row",["product"=>$product])

        @break
@endswitch