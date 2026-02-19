<div class="row">

    <div class="col-sm-12">
        {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}

        <div class="col-sm-12">
            <h5> {{$product->code." - ".$product->caption}}
            {{isset($product->product_version_id)?"(V".$product->version->version_code.")":""}}
            </h5>

            <hr>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{$tab=="edit"?"active":""}}  text-uppercase" id="home-tab" data-toggle="tab"
                       href="#home"
                       role="tab" aria-controls="home" aria-selected="false">اطلاعات پایه</a>
                </li>
                @if($product->product_service_type_id == 1)

                    <li class="nav-item">
                        <a class="nav-link {{$tab=="edit_supplementary"?"active":""}}  text-uppercase" id="edit_supplementary-tab" data-toggle="tab"
                           href="#edit_supplementary"
                           role="tab" aria-controls="edit_supplementary" aria-selected="false">اطلاعات تکمیلی</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{$tab=="sales"?"active":""}} text-uppercase" id="sales-tab"
                           data-toggle="tab" href="#sales"
                           role="tab"
                           aria-controls="sales" aria-selected="false">مشخصات فروش</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="warehouse"?"active":""}} text-uppercase" id="contact5-tab"
                           data-toggle="tab" href="#contact5"
                           role="tab"
                           aria-controls="contact5" aria-selected="false">انبارش</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="edit_classification"?"active":""}} text-uppercase"
                           id="contact-classification-tab"
                           data-toggle="tab" href="#contact-classification"
                           role="tab"
                           aria-controls="contact-classification" aria-selected="false">طبقه بندی</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="edit_property"?"active":""}} text-uppercase" id="contact-tab"
                           data-toggle="tab" href="#contact"
                           role="tab"
                           aria-controls="contact" aria-selected="false">مشخصات کالا</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{$tab=="consumed"?"active":""}} text-uppercase" id="consumed-tab"
                           data-toggle="tab" href="#consumed"
                           role="tab"
                           aria-controls="route" aria-selected="false">کالاهای مصرفی</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{$tab=="product_route"?"active":""}} text-uppercase" id="route-tab"
                           data-toggle="tab" href="#route"
                           role="tab"
                           aria-controls="route" aria-selected="false">مسیر محصول</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="route_property"?"active":""}} text-uppercase"
                           id="route_property-tab"
                           data-toggle="tab" href="#route_property"
                           role="tab"
                           aria-controls="route_property" aria-selected="false">مشخصات مسیر محصول </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="product_bom"?"active":""}}  text-uppercase" id="channel-tab"
                           data-toggle="tab" href="#channel"
                           role="tab"
                           aria-controls="channel" aria-selected="false">BOM</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="bom_permutation"?"active":""}}  text-uppercase"
                           id="bom_permutation-tab"
                           data-toggle="tab" href="#bom_permutation"
                           role="tab"
                           aria-controls="bom_permutation" aria-selected="false">کالاهای جایگزین تولید</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{$tab=="edit_replace_product"?"active":""}} text-uppercase"
                           id="province2-tab"
                           data-toggle="tab" href="#province2"
                           role="tab"
                           aria-controls="province2" aria-selected="false">کالای جایگزین مصرف</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="waste"?"active":""}}  text-uppercase" id="channel-tab"
                           data-toggle="tab" href="#waste"
                           role="tab"
                           aria-controls="waste" aria-selected="false">ضایعات</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{$tab=="edit_material_flow"?"active":""}} text-uppercase"
                           id="edit_material_flow-tab"
                           data-toggle="tab" href="#edit_material_flow"
                           role="tab"
                           aria-controls="edit_material_flow" aria-selected="false">جریان همبافتی (مواد)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="edit_lot_number"?"active":""}} text-uppercase"
                           id="edit_lot_number-tab"
                           data-toggle="tab" href="#edit_lot_number"
                           role="tab"
                           aria-controls="edit_lot_number" aria-selected="false">شماره لات </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="shade_number"?"active":""}} text-uppercase" id="shade_number-tab"
                           data-toggle="tab" href="#shade_number"
                           role="tab"
                           aria-controls="shade_number" aria-selected="false">شماره شید</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="edit_product_type"?"active":""}} text-uppercase"
                           id="edit_product_type-tab"
                           data-toggle="tab" href="#edit_product_type"
                           role="tab"
                           aria-controls="edit_product_type" aria-selected="false">بسته بندی</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="quality_control"?"active":""}} text-uppercase"
                           id="quality_control-tab"
                           data-toggle="tab" href="#quality_control"
                           role="tab"
                           aria-controls="quality_control" aria-selected="false">تنظیمات کنترل کیفیت</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=="planing"?"active":""}} text-uppercase"
                           id="planing-tab"
                           data-toggle="tab" href="#planing"
                           role="tab"
                           aria-controls="planing" aria-selected="false">برنامه ریزی</a>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{$tab=="actual_cost"?"active":""}} text-uppercase"
                       id="actual_cost-tab"
                       data-toggle="tab" href="#actual_cost"
                       role="tab"
                       aria-controls="edit_product_type" aria-selected="false">بهای تمام شده</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{$tab=="pricing"?"active":""}} text-uppercase"
                       id="pricing-tab"
                       data-toggle="tab" href="#pricing"
                       role="tab"
                       aria-controls="edit_product_type" aria-selected="false">قیمت گذاری</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{$tab=="version"?"active":""}} text-uppercase"
                       id="version-tab"
                       data-toggle="tab" href="#version"
                       role="tab"
                       aria-controls="edit_product_type" aria-selected="false">ورژن کالا</a>
                </li>
            </ul>
            <div class="tab-content " id="myTabContent">
                <div class="tab-pane fade {{$tab=="edit"?"show active ":""}}  " id="home" role="tabpanel"
                     aria-labelledby="home-tab">
                    @if($tab=="edit")
                        @include("line_product_station.product.init_info._init_info")
                    @else
                        <a href="{{route("line_product_station.product.edit",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>

                <div class="tab-pane fade {{$tab=="edit_supplementary"?"show active ":""}}  " id="edit_supplementary" role="tabpanel"
                     aria-labelledby="edit_supplementary-tab">
                    @if($tab=="edit_supplementary")
                        @include("line_product_station.product.init_info._init_info_supplementary")
                    @else
                        <a href="{{route("line_product_station.product.edit_supplementary",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>


                {{--                اطلاعات فروش--}}
                <div class="tab-pane fade {{$tab=="sales"?"show active ":""}}" id="sales" role="tabpanel"
                     aria-labelledby="sales-tab">
                    @if($tab=="sales")
                        @include("line_product_station.product.sale._info")
                    @else
                        <a href="{{route("line_product_station.product.sale.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif

                </div>
                <div class="tab-pane fade {{$tab=="edit_classification"?"show active ":""}}" id="contact-classification"
                     role="tabpanel"
                     aria-labelledby="profile-tab">

                    @if($tab=="edit_classification")
                        @include("line_product_station.product.classification._info")
                    @else
                        <a href="{{route("line_product_station.product.classification.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif

                </div>
                <div class="tab-pane fade {{$tab=="edit_property"?"show active ":""}}" id="contact" role="tabpanel"
                     aria-labelledby="contact-tab">
                    @if($tab=="edit_property")
                        @include("line_product_station.product.property._info")
                    @else
                        <a href="{{route("line_product_station.product.property.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>
                <div class="tab-pane fade {{$tab=="warehouse"?"show active ":""}}" id="contact5" role="tabpanel"
                     aria-labelledby="contact5-tab">
                    @if($tab=="warehouse")
                        @include("line_product_station.product.warehouse._info")
                    @else
                        <a href="{{route("line_product_station.product.warehouse.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>
                <div class="tab-pane fade  {{$tab=="consumed"?"show active ":""}}" id="consumed" role="tabpanel"
                     aria-labelledby="consumed-tab">
                    @if($tab=="consumed")

                        @include("line_product_station.product.consumed_product._info")
                    @else
                        <a href="{{route("line_product_station.product.consumed_product.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>
                <div class="tab-pane fade  {{$tab=="product_route"?"show active ":""}}" id="route" role="tabpanel"
                     aria-labelledby="route-tab">
                    @if($tab=="product_route")

                        @include("line_product_station.product.route._info")
                    @else
                        <a href="{{route("line_product_station.product.route.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>
                <div class="tab-pane fade  {{$tab=="route_property"?"show active ":""}}" id="route_property"
                     role="tabpanel"
                     aria-labelledby="route_property-tab">
                    @if($tab=="route_property")

                        @include("line_product_station.product.route_property._info")
                    @else
                        <a href="{{route("line_product_station.product.route_property.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>

                <div class="tab-pane fade {{$tab=="edit_replace_product"?"show active ":""}}" id="province2"
                     role="tabpanel" aria-labelledby="province2-tab">
                    @if($tab=="edit_replace_product")
                        @include("line_product_station.product.replace_product._info")
                    @else
                        <a href="{{route("line_product_station.product.replace_product.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>

                <div class="tab-pane fade {{$tab=="product_bom"?"show active ":""}}" id="channel" role="tabpanel"
                     aria-labelledby="channel-tab">
                    @if($tab=="product_bom")
                        @include("line_product_station.product.bom.bom._info")
                    @else
                        <a href="{{route("line_product_station.product.bom.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>


                <div class="tab-pane fade {{$tab=="bom_permutation"?"show active ":""}}" id="bom_permutation"
                     role="tabpanel"
                     aria-labelledby="bom_permutation-tab">
                    @if($tab=="bom_permutation")
                        @include("line_product_station.product.bom.bom_permutation._info")
                    @else
                        <a href="{{route("line_product_station.product.bom_permutation.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>

                <div class="tab-pane fade {{$tab=="waste"?"show active ":""}}" id="waste" role="tabpanel"
                     aria-labelledby="channel-tab">
                    @if($tab=="waste")
                        @include("line_product_station.product.waste._info")
                    @else
                        <a href="{{route("line_product_station.product.waste.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>


                <div class="tab-pane fade {{$tab=="edit_lot_number"?"show active ":""}}" id="edit_lot_number"
                     role="tabpanel" aria-labelledby="edit_lot_number-tab">
                    @if($tab=="edit_lot_number")
                        @include("line_product_station.product.lot_number._info")
                    @else
                        <a href="{{route("line_product_station.product.lot_number.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>
                <div class="tab-pane fade {{$tab=="shade_number"?"show active ":""}}" id="shade_number"
                     role="tabpanel" aria-labelledby="shade_number-tab">
                    @if($tab=="shade_number")
                        @include("line_product_station.product.shade_number._info")
                    @else
                        <a href="{{route("line_product_station.product.shade_number.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>

                <div class="tab-pane fade {{$tab=="edit_product_type"?"show active ":""}}" id="edit_product_type"
                     role="tabpanel" aria-labelledby="edit_product_type-tab">
                    @if($tab=="edit_product_type")
                        @include("line_product_station.product.packing_type._info")
                    @else
                        <a href="{{route("line_product_station.product.packing_type.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>


                <div class="tab-pane fade {{$tab=="quality_control"?"show active ":""}}" id="quality_control"
                     role="tabpanel" aria-labelledby="quality_control-tab">
                    @if($tab=="quality_control")
                        @include("line_product_station.product.quality_control._info")
                    @else
                        <a href="{{route("line_product_station.product.quality_control.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>



                <div class="tab-pane fade {{$tab=="planing"?"show active ":""}}" id="planing"
                     role="tabpanel" aria-labelledby="planing-tab">
                    @if($tab=="planing")
                        @include("line_product_station.product.planing._info")
                    @else
                        <a href="{{route("line_product_station.product.planing.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>

                {{--                جریان مواد--}}
                <div class="tab-pane fade {{$tab=="edit_material_flow"?"show active ":""}}" id="edit_material_flow"
                     role="tabpanel" aria-labelledby="edit_material_flow-tab">
                    @if($tab=="edit_material_flow" && isset($info))
                        @include("line_product_station.product.material_flow._".$info)
                    @elseif($tab=="edit_material_flow")
                        @include("line_product_station.product.material_flow._info")
                    @else
                        <a href="{{route("line_product_station.product.material_flow.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>

{{--                بهای تمام شده--}}
                <div class="tab-pane fade {{$tab=="actual_cost"?"show active ":""}}" id="actual_cost"
                     role="tabpanel" aria-labelledby="actual_cost-tab">
                    @if($tab=="actual_cost")
                        @include("line_product_station.product.actual_cost._info")
                    @else
                        <a href="{{route("line_product_station.product.actual_cost.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>

{{--                قیمت گذاری--}}
                <div class="tab-pane fade {{$tab=="pricing"?"show active ":""}}" id="pricing"
                     role="tabpanel" aria-labelledby="pricing-tab">
                    @if($tab=="pricing")
                        @include("line_product_station.product.pricing._info")
                    @else
                        <a href="{{route("line_product_station.product.pricing.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>



                {{--                ورژن کالا--}}
                <div class="tab-pane fade {{$tab=="version"?"show active ":""}}" id="version"
                     role="tabpanel" aria-labelledby="version-tab">
                    @if($tab=="version")
                        @include("line_product_station.product.version._info")
                    @else
                        <a href="{{route("line_product_station.product.version.index",$product)}}"
                           class="btn btn-primary"><i class="fa fa-undo"></i> بارگذاری </a>
                    @endif
                </div>


            </div>
        </div>
    </div>

</div>
