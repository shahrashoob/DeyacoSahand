@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  فروش ")

@section('content')
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5> {{$product->fullCaption()}} (
                        @switch($type)
                            @case ("other_packing_inventory")
                                سایر بسته بندی های مجاز موجود در انبار
                                @break
                            @case("packing_inventory")
                                بسته بندی های مجاز موجود در انبار
                                @break
                        @endswitch
                        )
                    </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>

                                <th>ردیف</th>
                                <th>تاریخ ایجاد بسته</th>
                                <th> شماره بسته بندی</th>
                                <th>نوع بسته بندی</th>
                                <th>مقدار کل</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem(); $sum=0;@endphp
                            @foreach($list as $item)
                                @php $sum+=$item->final_amount;@endphp
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        {{$item->get_create_date_and_time()}}
                                    </td>
                                    <td>
                                        <a
                                                href="{{route("fabric_raw.packing_form.view",$item->id)}}/{{$list->currentPage()}}"
                                                target="_blank">
                                            {{$item->code}}
                                        </a>


                                    </td>
                                    <td>
                                        {{$item->packing_type->caption??"---"}}
                                    </td>
                                    <td>
                                        {{$item->final_amount}}
                                    </td>

                                    <td>{{$item->status->caption??"---"}}</td>


                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4">جمع کل ({{$product->unit->caption}})</td>
                                <td>{{$sum}}</td>
                            </tr>
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
            <div class="col-md-12 center">
                <br/>
                <a href="{{route("sales.product_request_permission.index",$order_list->order_id)}}"
                   class="btn btn-outline-dark     " type="button">
                    بازگشت
                </a>


            </div>

        </div>

    </div>
@endsection

@section("styles")

@endsection
@section("scripts")

@endsection

