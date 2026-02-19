@php $list_lot_numbers=[];@endphp
<div class="col-md-6 offset-md-6">
    <div class="form-group">
        <label>

            @if(!isset($no_band))
                لات (های) باند {{$band_code}}:
            @else
                لات (ها)  :
            @endif
            @if(isset($productionFromItemLot))
                @foreach($productionFromItemLot as $item)
                    @if(isset($item->production_form_item->band_code) && $item->production_form_item->band_code == $band_code)
                        @include("line_product_station.product.lot_number._label",["lot_number"=>$item->lot_number,"id"=>$item->id,"amount"=>$item->amount,"product"=>$product??456,"unit_caption"=>$unit_caption??""])
                        ,
                        @php $list_lot_numbers[]=   $item; @endphp
                    @endif
                @endforeach
           @endif
        @foreach($list_lot_numbers as $item)

            @include("line_product_station.product.lot_number._collapse",["lot_number"=>$item->lot_number,"id"=>$item->id])

        @endforeach
    </div>
</div>


