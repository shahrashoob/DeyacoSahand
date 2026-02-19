@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("line_product_station.machine.public._search_view",["route"=>"fabric_raw.machine.index"])
            <div class="card">
                <div class="card-header">
                    <h5> لیست ماشین ها
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>
                                <th>خط -ایستگاه</th>
                                <th>گروه ماشین</th>
                                <th>کد و نام ماشین</th>
                                <th> وضعیت تولید</th>
                                <th>مقدار  <br/>کارت رزور</th>
                                <th>مقدار<br/> باقی مانده کل </th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>

                                    <td>
                                        {{$item->station->line->caption??""}} - {{$item->station->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->machine_type->caption??""}}
                                    </td>
                                    <td>
                                        <a href="{{route("fabric_raw.machine.view",$item)}}">{{$item->code}}
                                            - {{$item->caption}}</a>
                                    </td>
                                    <td>{{$item->production_status?$item->production_status->getCaption():"نامشخص"}}</td>
                                    <td>{{$item->getReserveAmount()}}</td>
                                    <td>{{$item->getReserveAmount()+$item->getRemainingAmountOfCurrentAllocation()}}</td>

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
