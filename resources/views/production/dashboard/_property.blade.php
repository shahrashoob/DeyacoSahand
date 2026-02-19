
        @if(isset($goods_kind_property_values_products[$item->product_id]))
            @foreach($goods_kind_property_values_products[$item->product_id] as $property_item)
                @switch($property1_show_in_production_dashboard)
                    @case(2)
                        <div style="border-radius: 50%;width: 20px;height:20px;background: {{$property_item->color}}; display: inline-block; margin: 3px"></div>

                        @break

                    @case(1)
                        {{$property_item->caption??$property_item->value}} -

                        @break

                @endswitch

                @switch($property2_show_in_production_dashboard)
                    @case(2)
                        <div style="border-radius: 50%;width: 20px;height:20px;background: {{$property_item->color}}; display: inline-block; margin: 3px"></div>

                        @break

                    @case(1)
                        {{$property_item->caption??$property_item->value}} -

                        @break

                @endswitch
            @endforeach
        @endif

