@php $has_any_machines=false; $html=""; @endphp
@foreach($production->product->route()->where("active_status_id",1200)->get() as $product_route)
    @php
        /**
                * برای اینکه اگر یک کالا در اولین تخصیص آن مسیر های یک سانی داشت، یکی از آنها را نمایش دهد.   *
     */
             // گرفتن اولین آیتم مسیر محصول
                    $line_product_station=
                        $product_route->line_product_station()->
                        where("status_id",1200)->
                        orderBy("priority_number")->
                        first();
    if(!$line_product_station)
         continue;
        if(!isset($machine_type_priority)){
                $machine_type_priority=[];
            }
            if(!isset($machine_type_priority[$line_product_station->priority_id])){
                $machine_type_priority[$line_product_station->priority_id]=[];
            }
            if(!isset($machine_type_priority[$line_product_station->priority_id][$line_product_station->machine_type_id])){
                $machine_type_priority[$line_product_station->priority_id][$line_product_station->machine_type_id]=0;
            }
                $machine_type_priority[$line_product_station->priority_id][$line_product_station->machine_type_id]+=1;

    @endphp

    @if($line_product_station && $machine_type_priority[$line_product_station->priority_id][$line_product_station->machine_type_id]==1)

        {{--        @include("goods_kind_process.general.production_card.machine_allocation._line_product_station_panel")--}}

        @php
            $html.= view(
                'goods_kind_process.general.production_card.machine_allocation._line_product_station_panel',
                [
                    'line_product_station' => $line_product_station ?? null,
                    "route_path"=>$route_path,
                    "production"=>$production
                ]
            )->render();
            if($html){
                $has_any_machines=true;
            }
        @endphp

    @endif
@endforeach

@if(!$has_any_machines)
    <div class="col-md-12">
        <div class="alert alert-warning">

            با توجه به کانال تولید کالا در مسیرهای مختلف محصول (طراحی کالا) و کانال تولید مجاز ماشین ها هیچ مسیری برای
            تخصیص
            وجود ندارد.
            <br/>
            لطفا موارد ذیل را چک نمایید:
            <ul>
                <li>
                    کانال تولید مجاز مسیرهای مختلف محصول
                </li>
                <li>
                    کانال های تولید مجاز ماشین ها یا گروه های ماشین
                </li>
                <li>
                    فعال بودن مسیر محصول کالا
                </li>
                <li>
                    وضعیت تولید ماشین
                    @if($line_product_station)
                    (وضعیت های مجاز تخصیص:
                    @php $k=0; @endphp

                    @foreach($line_product_station->machine_type->machine_status as $item)
                        @if($item->possibility_of_allocation_machine)
                            @if(++$k > 1)
                                ,
                            @endif
                            {{$item->production_status->caption}}

                        @endif
                    @endforeach
                    )
                    @else
                        (هیچ گروه ماشینی برای کالا تعریف نشده است، لطفا اطلاعات مسیر محصول کالا را بررسی کنید.)
                    @endif

                </li>
            </ul>
        </div>
    </div>
@else
    <div class="col-md-12">
        <div class="alert alert-info">
            کاربر گرامی، شما می توانید با توجه به کانال تولید مسیرهای مختلف محصول (طراحی کالا) و کانال های تولید مجاز
            ماشین و وضعیت های ماشین
            ها یکی از ماشین های فوق را انتخاب نمایید.

        </div>
    </div>
    <br/>
    {!! $html !!}
@endif
