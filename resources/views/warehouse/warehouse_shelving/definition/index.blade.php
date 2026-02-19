@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست سایت های
                        {{$warehouse->caption}}
                        <a class="btn btn-success" href="{{route("wh.warehouse_shelving.definition.create",$warehouse)}}"> <i
                                class="fa fa-plus"></i> افزودن سایت جدید به انبار </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد سایت</th>
                                <th> نام سایت</th>
                                <th>آیا کالا به صورت مستقیم می تواند <br/>در این سایت قرار بگیرید؟</th>
                                <th></th>
                                <th>سالن ها</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->fullCode()}}

                                    </td>
                                    <td>
                                        <a href="{{route("wh.warehouse_shelving.definition.edit",[$warehouse,$item])}}"> {{$item->caption}} </a>
                                    </td>

                                    <td>
                                        {{$item->can_product_directly_in_location==1?"بله":"خیر"}}
                                    </td>
                                    <th>
                                        &nbsp;
                                        &nbsp; <span class="text-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                                       aria-haspopup="true" style="width: 140px"
                                                       aria-expanded="false">

                                                  چاپ / دانلود لیبل

                                        </span>
                                        <div class="dropdown-menu" style="text-align: center">
                                            <a  href="{{route("wh.warehouse_shelving.definition.shelving_download_label",[$warehouse,$item,15])}}"> دانلود لیبل 5*8 </a>

                                            <br/>
                                            <a  href="{{route("wh.warehouse_shelving.definition.shelving_download_label",[$warehouse,$item,2])}}"> دانلود لیبل 9*13 </a>



                                     <br/>
                                            <a  href="{{route("wh.warehouse_shelving.definition.shelving_print_label",[$warehouse,$item,15])}}"> چاپ لیبل 5*8 </a>

                                            <br/>
                                            <a  href="{{route("wh.warehouse_shelving.definition.shelving_print_label",[$warehouse,$item,2])}}"> چاپ لیبل 9*13 </a>



                                        </div>

                                        &nbsp;
                                        &nbsp;@if($allow_view_qr)
                                        &nbsp;
                                        <a  href="{{route("wh.warehouse_shelving.dashboard.view_qr",[$item])}}" target="_blank"> <i class="fa fa-eye"> </i> </a>
                                        &nbsp;
                                        &nbsp;@endif
                                    </th>
                                    <td>
                                        <a href="{{route("wh.warehouse_shelving.definition.list",[$warehouse,$item])}}">
                                         {{$item->items()->count()}} سالن ها
                                        </a>
                                        <a class="text-success" href="{{route("wh.warehouse_shelving.definition.create_sub_line",[$warehouse,$item])}}"> <i class="fa fa-plus-circle"> </i> </a>

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
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
