@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست  نمود های بیرونی نقص ماشین

                        <a class="btn btn-success" href="{{route("line_product_station.machine.fault.machine_fault_sign.create")}}"> <i
                                class="fa fa-plus"></i> افزودن نمود بیرونی نقص ماشین  </a>
                    </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> عنوان</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("line_product_station.machine.fault.machine_fault_sign.edit",$item)}}">{{$item->caption}}</a>
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
        <div class="col-md-12 center">
            <br/>
            <a href="{{route("line_product_station.machine.fault.machine_fault.index")}}"
               class="btn btn-outline-dark">بازگشت</a>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
