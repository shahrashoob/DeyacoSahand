@if(isset($is_mobile) && $is_mobile)
    @include("production.public_module.register_production._add_packing_item_mobile")
@else
    @include("production.public_module.register_production._add_packing_item_desktop")
@endif