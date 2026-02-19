@php $case_id=$product->supply_type_id;
if($product->goods_kind->production_algorithm_type_id==3){
	$case_id=-1;
}
@endphp
@switch($case_id)
    @case(1)
    <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf
        <div class="col-sm-12 mb-3">

            <div class="accordion" id="accordionExample">
                @foreach($product->route as $route)

                    <div class="card">
                        <div
                            class="card-header" {{$route->active_status_id!=1200 ? 'style=background:#1e3953!important':""}} >
                            <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route{{$route->id}}"
                                                aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                    ویژگی های
                                    <b> {{$route->caption}}</b>

                                    برای
                                    <b>{{$product->caption}}</b>

                                </a>
                            </h5>
                            <div class="card-header-right" style="margin-top: 10px">

                            </div>
                        </div>
                        <div class="multi-collapse collapse" id="route{{$route->id}}" data-parent="#accordionExample">

                            @include("line_product_station.product.route_property._machine_product_property")
                        </div>
                    </div>

                @endforeach
            </div>
        </div>

        @include($view_path."_btn_list_1")
    </form>
    @break
    @case(-1)
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning">
                با توجه به نوع روش برنامه ریزی تولید در رسته کالایی، امکان تعریف کالاهای مصرفی برای این کالا وجود ندارد.
            </div>


        </div>
    </div>

    @include($view_path."_btn_list_2")

    @break
    @default
    <div class="col-sm-12">
        <div class="alert alert-warning">
            با توجه به نوع تامین کالا، امکان تعریف مشخصات مسیر محصول برای این کالا وجود ندارد.
        </div>
        @include($view_path."_btn_list_2")

    </div>

    @break

@endswitch
