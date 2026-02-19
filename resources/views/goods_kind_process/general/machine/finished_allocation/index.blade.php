@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>سابقه تخصیص های انجام شده بر روی ماشین {{$machine->fullCaption()}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead >
                            <tr>
                                <th>#</th>
                                <th>شماره تخصیص</th>
                                <th>کارت تولید</th>
                                <th>تراکنش ها مصرف</th>
                                <th> کالا</th>
                                <th></th>
                                <th>مقدار کارت تولید</th>
                                <th>مقدار تخصیص</th>
                                <th>وضعیت کارت تولید</th>
                                <th>وضعیت تخصیص</th>
                                <th>تعداد داف</th>
                                <th>مقدار هر داف</th>
                                <th>تاریخ رزرو</th>
                                <th>تاریخ شروع تولید</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem()-1;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->allocation_id}}</td>
                                    <td>

                                        <a href="{{route($production_route."view_card",$item->production)}}">{{$item->production->serial()}}</a>
                                    </td>
                                    <td>
                                        <a href="{{route($route_path."material_consumed_list",[$machine->id,$item->allocation_id])}}">
                                            {{$item->allocation->material_consumed()->count()}}
                                            تراکنش</a>

                                    </td>
                                    <td>
                                        {{$item->product->fullCaption()}}
                                    </td>
                                    <td>
                                        @if($item->version_code)

                                            V{{$item->version_code}}
                                        @endif
                                    </td>
                                    <td>
                                        {{$item->production->number}} {{$item->product->unit->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->production->get_allocation_amount($machine->id,1,$item->allocation_id)}} {{$item->product->unit->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->production->getStatus()}}
                                    </td>
                                    <td>
                                        {{$item->status->caption??"-"}}
                                    </td>
                                    <td>
                                        {{$item->max_number_of_doffs}}
                                    </td>
                                    <td>
                                        {{$item->amount_of_each_doffs}}
                                    </td>
                                    <td>
                                        {{$item->get_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->start_time()}}
                                    </td>
                                    <th>

                                    </th>


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
            <a href="{{route($dashboard_route."view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>

        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
