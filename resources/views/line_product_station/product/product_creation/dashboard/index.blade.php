@extends('layouts.admin._master')
@section("page_header_title"," داشبورد طراحی کالا ")
@section("content")
    <div class="row">
        @include('component.input.datepicker.jalali_datepicker._style')
        <div class="col-sm-12">
            @include("line_product_station.product.product_creation.dashboard._search_view",["route"=>"line_product_station.product.product_creation.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست همه درخواست های طراحی</h5>

                    @if (  $post_user->checkButtonPermission(  "line_product_station.product.product_creation.new_form.index" ) )
                        <a class="btn btn-outline-success"
                           href="{{route("line_product_station.product.product_creation.new_form.index")}}"> <i
                                    class="fa fa-plus"></i> افزودن درخواست طراحی کالا </a>
                    @endif

                    @if (  $post_user->checkButtonPermission(  "line_product_station.product.product_creation.new_form_quick.index" ) )

                        <a href="{{route("line_product_station.product.product_creation.new_form_quick.index")}}"
                           class="btn btn-outline-success"> <i
                                    class="fa fa-plus"></i>
                            طراحی سریع کالای مشابه</a>
                    @endif
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد</th>
                                <th>درخواست دهنده</th>
                                <th>کد کالا</th>
                                <th>نام کالا</th>
                                <th>ورژن</th>
                                <th> نام پیشنهادی کالا</th>

                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.product.product_creation.dashboard.view",$item)}}">
                                            {{$item->getCode()}}
                                        </a>
                                    </td>
                                    <td>{{$item->worker?$item->worker->fullName():"**"}}</td>
                                    <td>{{$item->product->code??""}}</td>
                                    <td>
                                        @if($item->product)
                                            <a href="{{route("line_product_station.product.product_creation.product_show.index",$item)}}">
                                                {{$item->product->caption??""}}
                                            </a>
                                        @else
                                            {{$item->product->caption??""}}

                                        @endif

                                    </td>
                                    <td>
                                        {{isset($item->product->product_version_id)?"V".($item->product->version->version_code??"")."":""}}
                                    </td>
                                    <td>{{$item->caption??""}}</td>

                                    <td>
                                        <a href="{{route("line_product_station.product.product_creation.dashboard.log",$item)}}">
                                            {{$item->status->caption??""}}
                                        </a>
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
    @include('component.input.datepicker.jalali_datepicker._script')
@endsection


