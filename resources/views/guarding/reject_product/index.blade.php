@extends('layouts.admin._master')
@section("page_header_title","داشبورد نگهبانی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست درخواست های مرجوعی</h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره فرم مرجوعی</th>
                                <th>شماره برگ خروج</th>
                                <th>تاریخ ایجاد</th>
                                <th>تعداد بسته بندی</th>
                                <th>وضعیت</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $i=1;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>
                                        <a href="{{route("guarding.reject_product.view",$item)}}">
                                            {{$item->getCode()}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$item->exit_form->code}}
                                    </td>
                                    <td>{{$item->get_create_date_and_time()}}</td>
                                    <td>{{$item->items()->count()}}</td>
                                    <td>

                                        {{$item->status->caption}}

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
