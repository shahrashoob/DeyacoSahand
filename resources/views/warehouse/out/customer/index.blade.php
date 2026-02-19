@extends('layouts.admin._master')
@section("page_header_title","داشبورد جاری فروش  / ثبت سفارش برای مشتری")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("warehouse.out.customer._search_view",["route"=>"wh.out.customer.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست درخواست های خروج از انبار به تفکیک مشتریان</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد </th>
                                <th>نام مشتری</th>
                                <th> استان</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->code??""}}</td>
                                    <td>
                                        <a  href="{{route("wh.out.customer.view",$item)}}">


                                        {{$item->caption??""}}
                                            </a>
                                    </td>
                                    <td>{{$item->channelType->caption??""}}</td>
                                    <td>{{$item->province->caption??""}}</td>
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
