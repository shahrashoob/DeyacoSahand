@php $case_id=$product->supply_type_id;
if($product->goods_kind->production_algorithm_type_id==3){
	$case_id=-1;
}
@endphp
@switch($case_id)
    @case(2)
    @case(4)
        <div class="col-sm-12">
            <div class="alert alert-warning">
                با توجه به نوع تامین کالا، امکان تعریف BOM برای این کالا وجود ندارد.
            </div>

        </div>
        @break
    @case(1)
    @case(3)
        <div class="col-sm-12 mb-3">
            <h5 class="mb-3">لیست BOM های {{$product->fullCaption()}}
            </h5>

            <hr>
            <div class="accordion" id="accordionExample">
                @foreach($product->route()->where("active_status_id",1200)->get() as $route)

                    <div class="card">
                        <div
                                class="card-header" {{$route->active_status_id!=1200 ? 'style=background:#1e3953!important':""}} >
                            <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route{{$route->id}}"
                                                aria-expanded="false" aria-controls="collapseOne" class="collapsed">

                                    BOM های {{$route->caption}}


                                </a>
                            </h5>
                            <div class="" style="margin-top: 10px; display: inline-block">
                                <a class="text-primary text-success"
                                   href="{{route($route_path."create",[$route,$product_creation_process])}}"
                                   onclick="return confirm('در صورت تایید که BOM جدید به صورت اتومات تعریف می گردد.')"><i
                                            class="fa fa-plus"></i> BOM جدید </a>

                            </div>
                        </div>
                        <div class="multi-collapse collapse {{$route->code==($show_route_code??"01")?"show":""}}"
                             id="route{{$route->id}}"
                             style=""
                             data-parent="#accordionExample">
                            @if(count($route->bom) ==0)
                                <br/>
                                <div class="alert alert-warning">برای {{$route->caption}} هیچ BOMی تعریف نشده است.</div>
                            @endif
                            @foreach($route->bom as $bom)
                                <div class="col-sm-12">
                                    <br/>
                                    <h5 style="display: inline">
                                        {{$bom->fullCaption()}} ({{$bom->active_status->caption}})


                                    </h5>
                                    <a class="text-primary "
                                       href="{{route($route_path."edit",[$bom,$product_creation_process])}}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="text-primary text-danger"
                                       onclick="return confirm('آیا از حذف BOM اطمینان دارید')"
                                       href="{{route($route_path."destroy",[$product,$bom,$product_creation_process])}}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a class="text-primary text-success"
                                       href="{{route("line_product_station.product.bom_item.create",[$bom,$product_creation_process])}}">
                                        <i class="fa fa-plus"></i> افزودن ردیف جدید
                                    </a>
                                    <a class="text-primary text-primary"
                                       href="{{route("line_product_station.product.bom_log.index",[$bom,$product_creation_process])}}">
                                        <i class="fa fa-plus"></i> مشاهده سابقه BOM
                                    </a>
                                    @if($bom->active_status_id==1200)
                                        <div class="card">
                                            <div class="card-body" style="border: 3px solid #0b2e13">
                                                @include("line_product_station.product.bom.bom_item._bom_items")
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                            <br/>
                        </div>
                    </div>

                @endforeach
            </div>

        </div>
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
        @break

@endswitch

@if($view_path)
    @include($view_path."_btn_list")
@endif
