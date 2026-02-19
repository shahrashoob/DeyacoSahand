@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست  نقص های ماشین
                        <a class="btn btn-success" href="{{route("line_product_station.machine.fault.machine_fault.create")}}"> <i
                                class="fa fa-plus"></i> افزودن نقص ماشین </a>
                        <a class="btn btn-primary" href="{{route("line_product_station.machine.fault.machine_fault_sign.index")}}">   نمودهای بیرونی نقص ماشین </a>
                    </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد</th>
                                <th> عنوان</th>
                                <th>نمودهای بیرونی نقص ماشین</th>
                                <th> بعد از اعلام نقص، آیا نیاز به تایید دارد؟</th>
                                <th> بعد از رفع نقص، آیا نیاز به تایید دارد؟</th>
                                <th> تایید کننده نقص</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->getCode()}}</td>


                                    <td>
                                        <a href="{{route("line_product_station.machine.fault.machine_fault.edit",$item)}}">{{$item->caption}}</a>
                                    </td>
                                    <td>
                                        @foreach($item->machine_fault_signs as $machine_fault_machine_fault_sign)
                                            {{$machine_fault_machine_fault_sign->machine_fault_sign->caption}},
                                        @endforeach
                                    </td>
                                    <td>{!! $item->need_to_confirmation?"<i class='fa fa-check'></i>" :""!!}</td>
                                    <td>{!! $item->need_to_confirmation_for_fix?"<i class='fa fa-check'></i>" :""!!}</td>
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
