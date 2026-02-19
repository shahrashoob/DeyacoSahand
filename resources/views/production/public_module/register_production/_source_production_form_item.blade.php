@if( $source_production_form_item)
    <div  class="alert alert-info">
        شما در حال ثبت تولید برای آیتم فرم تولید
        {{$source_production_form_item->code}}
        با کالای
        <b> {{$source_production_form_item->product->caption}}</b>
        و مقدار
        {{$source_production_form_item->final_amount}}  {{$source_production_form_item->product->unit->caption}}
        می باشید.


        <a class=""
           href="{{route("production.public_module.register_production.end_of_source_production_form_item",[$machine_allocation,$source_production_form_item->id])}}">

            پایان آیتم فرم تولید

        </a>
    </div>
@endif