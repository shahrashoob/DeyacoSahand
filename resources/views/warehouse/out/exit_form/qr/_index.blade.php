@php
   $random_form= $form->getRandom();
        $product_request_form_form= $form->getAllProductRequestFormCodes("first_form_form");
         $sum_amount     =$form->item()->sum( "amount");
            $sum_sub_amount = $form->item()->sum( "sub_amount") ;
            $unit=$form->item->first()->product->unit;
            $sub_unit=$form->item->first()->product->sub_unit;
@endphp

@if(isset($show_packing_form))
    @include("warehouse.out.exit_form.qr._panel_info")
    @if(isset($list_group_by_packing_form))
        @include($list_group_by_packing_form)
    @else
        @include("warehouse.out.exit_form.qr._list_group_by_packing_form")
    @endif
@else
    @include("warehouse.out.exit_form.qr._panel_info")
    @include("warehouse.out.exit_form.qr._list_group_by_products")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>
                    <a href="{{route("wh.show_output_packing_form",[$form , $form->random])}}">
                        آیتم های برگ خروج به تفکیک بسته بندی
                    </a>
                </h5>
            </div>
        </div>
    </div>



    @include("warehouse.dashboard._log")
@endif
