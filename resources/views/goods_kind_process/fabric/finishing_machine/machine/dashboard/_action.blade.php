@include("goods_kind_process.fabric.special_production.machine.dashboard._action")

@if(isset($machine_allocation) && $machine_allocation && $machine_allocation->line_product_station && $machine_allocation->line_product_station->has_control_sample
&& in_array($machine->production_status_id,[7303902, 7303903])
)

    <a class="btn btn-primary"
       href="{{route("fabric.finishing_machine.machine.control_sample.index",[$machine])}}">
        مشاهده مقادیر مورد نیاز (فرم شاهد)
    </a>


@endif

{{--اگر عملیات بعدی STS است، باید اپراتور شروع عملیات بعدی را بزند--}}
@if($machine->production_status_id ==7303903 && isset($start_to_start_line_product_station) && $start_to_start_line_product_station)
    <a class="btn btn-success"
       href="{{route("fabric.finishing_machine.machine.start_to_start.index",[$machine])}}">شروع عملیات در
    {{$start_to_start_line_product_station->machine_type->caption}}
    </a>
@endif

