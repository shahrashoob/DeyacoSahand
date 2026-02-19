@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")

    @php $sub_caption=$warehouseShelving->get_next_warehouse_shelving_type()->sub_caption??""@endphp
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>
                        {{$warehouseShelving->fullCaption()}}
                    </h5>

                    <a class="text-success"
                       href="{{route("wh.warehouse_shelving.definition.create_sub_line",[$warehouse,$warehouseShelving])}}">
                        <i
                                class="fa fa-plus"></i>
                        افزودن {{$warehouseShelving->warehouse_shelving_type->sub_caption}} جدید </a>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد {{$warehouseShelving->warehouse_shelving_type->sub_caption}}  </th>
                                <th> نام {{$warehouseShelving->warehouse_shelving_type->sub_caption}}  </th>
                                <th>
                                    آیا کالا به صورت مستقیم <br/>می تواند در این
                                    نام {{$warehouseShelving->warehouse_shelving_type->sub_caption}} قرار بگیرد؟
                                </th>
                                <th></th>
                                <th>{{$sub_caption==""?"":$sub_caption." ها"}}   </th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->warehouse_shelving_line_status_id==1200?$item->fullCode():"---"}}
                                    </td>

                                    <td>
                                        <a href="{{route("wh.warehouse_shelving.definition.edit",[$warehouse,$item])}}"> {{$item->caption}}</a>
                                    </td>
                                    <td>
                                        {{$item->can_product_directly_in_location==1?"بله":"خیر"}}
                                    </td>
                                    <th>
                                        &nbsp;
                                        &nbsp; <span class="text-primary dropdown-toggle" type="button"
                                                     data-toggle="dropdown"
                                                     aria-haspopup="true" style="width: 140px"
                                                     aria-expanded="false">چاپ / دانلود لیبل
                                        </span>
                                        <div class="dropdown-menu" style="text-align: center">
                                            <a href="{{route("wh.warehouse_shelving.definition.shelving_download_label",[$warehouse,$item,15])}}">
                                                دانلود لیبل 5*8 </a>

                                            <br/>
                                            <a href="{{route("wh.warehouse_shelving.definition.shelving_download_label",[$warehouse,$item,2])}}">
                                                دانلود لیبل 9*13 </a>


                                            <br/>

                                            &nbsp;
                                            <a href="{{route("wh.warehouse_shelving.definition.shelving_print_label",[$warehouse,$item,15])}}">
                                                چاپ لیبل 5*8 </a>

                                            <br/>
                                            <a href="{{route("wh.warehouse_shelving.definition.shelving_print_label",[$warehouse,$item,2])}}">
                                                چاپ لیبل 9*13 </a>


                                        </div>
                                        &nbsp;@if($allow_view_qr)

                                            <a href="{{route("wh.warehouse_shelving.dashboard.view_qr",[$item])}}" target="_blank"> <i
                                                        class="fa fa-eye"> </i> </a>
                                            &nbsp;
                                            &nbsp;
                                        @endif
                                    </th>
                                    <td>
                                        @if($sub_caption!="")
                                            <a href="{{route("wh.warehouse_shelving.definition.list",[$warehouse,$item])}}">
                                                {{$item->items()->count()}} {{$warehouseShelving->get_next_warehouse_shelving_type()->sub_caption??""}}
                                            </a>
                                            <a class="text-success"
                                               href="{{route("wh.warehouse_shelving.definition.create_sub_line",[$warehouse,$item])}}">
                                                <i class="fa fa-plus-circle"> </i> </a>
                                        @endif
                                    </td>
                                    <td>{{$item->status->caption??""}}</td>
                                    <td>

                                    </td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$list->firstItem()}}</b>
                        تا
                        <b>{{$list->lastItem()}}</b>
                        از
                        <b>{{$list->total()}}</b>
                        رکورد موجود
                    </div>
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
                <div class="text-center">
                    @if($warehouseShelving->parent_id)
                        <a class="btn btn-outline-dark"
                           href="{{route("wh.warehouse_shelving.definition.list", [$warehouse, $warehouseShelving->parent_id])}}">
                            بازگشت </a>
                    @else
                        <a class="btn btn-outline-dark"
                           href="{{route("wh.warehouse_shelving.definition.index",$warehouse)}}"> بازگشت </a>

                    @endif
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
