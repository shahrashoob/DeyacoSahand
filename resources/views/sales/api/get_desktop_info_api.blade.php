



            @include("sales.dashboard._special_panel")


        @if($post_user->checkButtonPermission("sales.show_order_factor_products"))
            @include("customer.group.buy._order_factor_products")
        @endif







