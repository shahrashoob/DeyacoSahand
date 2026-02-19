<div class="dropdown drp-user show">


    <a href="#" class="dropdown-toggle " data-toggle="dropdown"
       aria-expanded="true">
        {{$item->product->code." - ".$item->product->caption}}

    </a>
    <div class="dropdown-menu dropdown-menu-right profile-notification ">

        <div class="row">
            <div class="col-md-12" style="text-align: center; font-size: 13px; font-weight: normal">
             @if(isset($consume_products[$item->product_id]))
                    @foreach($consume_products[$item->product_id] as $consume_product)
                            {{$consume_product}}
                        <br/>
                    @endforeach
             @endif

            </div>

        </div>
    </div>


</div>
