@php $case_id=$product->supply_type_id;
if($product->goods_kind->production_algorithm_type_id==3){
	$case_id=-1;
}
@endphp
@switch($case_id)
    @case(1)
        <div class="col-sm-12 mb-3">
            <h5 class="mb-3">لیست مسیرهای تولید {{$product->code."-".$product->caption}}

                <a class="btn btn-outline-success"
                   href="{{route($route_path."create",[$product,$product_creation_process])}}">افزودن
                    مسیر جدید</a>
            </h5>

            <hr>
            <div class="accordion" id="accordionExample">
                @php $k=0;@endphp
                @foreach($product->route as $route)

                    <div class="card">
                        <div
                            class="card-header" {{$route->active_status_id!=1200 ? 'style=background:#1e3953!important':""}} >
                            <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route{{$route->id}}"
                                                aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                    {{$route->fullCaption()}}

                                </a>
                            </h5>
                            <div  style="margin-top: 10px; display: inline">
                                <a class="text-primary text-success"
                                   href="{{route("line_product_station.product.product_station.create",[$product,$route,$product_creation_process])}}"><i
                                        class="fa fa-plus"></i> افزودن ردیف جدید </a>
                                <a class="text-primary "
                                   href="{{route($route_path."edit",[$product,$route,$product_creation_process])}}"><i
                                        class="fa fa-edit"></i> </a>
                                <a class="text-primary text-danger"
                                   href="{{route($route_path."destroy",[$product,$route,$product_creation_process])}}"><i
                                        class="fa fa-trash"></i> </a>
                            </div>
                        </div>
                        <div class="multi-collapse collapse " id="route{{$route->id}}" data-parent="#accordionExample">
                            @include("line_product_station.product.route._line_product_info")
                        </div>
                    </div>

                @endforeach
            </div>
        </div>

        @include($view_path."_btn_list")

        @break
    @case(2)
        <div class="col-sm-12 mb-3">
            <h5 class="mb-3">لیست تامین کنندگان {{$product->code."-".$product->caption}}

                <a class="btn btn-outline-success"
                   href="{{route($route_path."create",[$product,$product_creation_process])}}">افزودن
                    تامین کننده جدید</a>
            </h5>

            <hr>
            <div class="accordion" id="accordionExample">
                @foreach($product->route as $route)

                    <div class="card">
                        <div
                            class="card-header" {{$route->active_status_id!=1200 ? 'style=background:#1e3953!important':""}} >
                            <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route{{$route->id}}"
                                                aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                    {{$route->fullCaption()}}

                                </a>
                            </h5>
                            <div  style="margin-top: 10px; display: inline">
                                <a class="text-primary text-success"
                                   @if($product->supply_type_id!=2 || ($product->supply_type_id == 2 && count($route->line_product_station)==0 ) )
                                       href="{{route("line_product_station.product.product_station.create",[$product,$route,$product_creation_process])}}"><i
                                        class="fa fa-plus"></i> افزودن ردیف جدید </a>
                                @endif
                                <a class="text-primary "
                                   href="{{route($route_path."edit",[$product,$route,$product_creation_process])}}"><i
                                        class="fa fa-edit"></i> </a>
                                <a class="text-primary text-danger"
                                   href="{{route($route_path."destroy",[$product,$route,$product_creation_process])}}"><i
                                        class="fa fa-trash"></i> </a>
                            </div>
                        </div>
                        <div class="multi-collapse collapse" id="route{{$route->id}}" data-parent="#accordionExample">
                            @include("line_product_station.product.route._line_product_info")
                        </div>
                    </div>

                @endforeach
            </div>
        </div>

        @include($view_path."_btn_list")

        @break
    @case(4)
        <div class="col-sm-12 mb-3">
            <h5 class="mb-3">لیست مسیرهای دریافت امانی  {{$product->code."-".$product->caption}}

                <a class="btn btn-outline-success"
                   href="{{route($route_path."create",[$product,$product_creation_process])}}">افزودن
                    مسیر جدید</a>
            </h5>

            <hr>
            <div class="accordion" id="accordionExample">
                @foreach($product->route as $route)

                    <div class="card">
                        <div
                                class="card-header" {{$route->active_status_id!=1200 ? 'style=background:#1e3953!important':""}} >
                            <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route{{$route->id}}"
                                                aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                    {{$route->fullCaption()}}

                                </a>
                            </h5>
                            <div  style="margin-top: 10px; display: inline">
                                <a class="text-primary text-success"
                                   href="{{route("line_product_station.product.product_station.create",[$product,$route,$product_creation_process])}}"><i
                                            class="fa fa-plus"></i> افزودن ردیف جدید </a>
                                <a class="text-primary "
                                   href="{{route($route_path."edit",[$product,$route,$product_creation_process])}}"><i
                                            class="fa fa-edit"></i> </a>
                                <a class="text-primary text-danger"
                                   href="{{route($route_path."destroy",[$product,$route,$product_creation_process])}}"><i
                                            class="fa fa-trash"></i> </a>
                            </div>
                        </div>
                        <div class="multi-collapse collapse" id="route{{$route->id}}" data-parent="#accordionExample">
                            @include("line_product_station.product.route._line_product_info")
                        </div>
                    </div>

                @endforeach
            </div>
        </div>

        @include($view_path."_btn_list")

        @break

    @case(3)
        <div class="col-sm-12 mb-3">
            <h5 class="mb-3">لیست مسیرهای پیمانکاری {{$product->code."-".$product->caption}}

                <a class="btn btn-outline-success"
                   href="{{route($route_path."create",[$product,$product_creation_process])}}">افزودن
                    مسیر جدید</a>
            </h5>

            <hr>
            <div class="accordion" id="accordionExample">
                @foreach($product->route as $route)

                    <div class="card">
                        <div
                            class="card-header" {{$route->active_status_id!=1200 ? 'style=background:#1e3953!important':""}} >
                            <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route{{$route->id}}"
                                                aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                    {{$route->fullCaption()}}

                                </a>
                            </h5>
                            <div class="card-header-right" style="margin-top: 10px; display: inline">
                                <a class="text-primary text-success"
                                   href="{{route("line_product_station.product.product_station.create",[$product,$route,$product_creation_process])}}"><i
                                        class="fa fa-plus"></i> افزودن ردیف جدید </a>
                                <a class="text-primary "
                                   href="{{route($route_path."edit",[$product,$route,$product_creation_process])}}"><i
                                        class="fa fa-edit"></i> </a>
                                <a class="text-primary text-danger"
                                   href="{{route($route_path."destroy",[$product,$route,$product_creation_process])}}"><i
                                        class="fa fa-trash"></i> </a>
                            </div>
                        </div>
                        <div class="multi-collapse collapse" id="route{{$route->id}}" data-parent="#accordionExample">
                            @include("line_product_station.product.route._line_product_info")
                        </div>
                    </div>

                @endforeach
            </div>
        </div>

        @include($view_path."_btn_list")

        @break

    @case(-1)
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-warning">
                    با توجه به نوع روش برنامه ریزی تولید در رسته کالایی، امکان تعریف کالاهای مصرفی برای این کالا وجود
                    ندارد.
                </div>


            </div>
        </div>

        @include($view_path."_btn_list")
        @break
@endswitch
