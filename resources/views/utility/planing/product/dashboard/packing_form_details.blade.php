@extends('layouts.admin._master')
@section("page_header_title"," داشبورد برنامه ریزی - لیست کالاها ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>موجودی
                        <b>{{$product->fullCaption()}}</b>
                        به تفکیک کد بسته بندی
                        ({{$packing_type->caption}})
                    </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد بسته بندی</th>
                                <th>تاریخ ایجاد</th>
                                <th> مقدار</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$packing_forms->firstItem();$sum=0;@endphp
                            @foreach($packing_forms as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a id="dcpk{{$item->id}}" target="_blank"
                                           href="{{route("fabric_raw.packing_form.view",$item->packing_form_id)}}">


                                            {{$item->code}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$item->get_create_date_and_time()}}


                                    </td>

                                    <td>{{$item->final_amount}}</td>
                                    @php $sum+=$item->final_amount;@endphp
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3"></td>
                                <td colspan="1">{{$sum}}</td>
                            </tr>
                            </tbody>

                        </table>
                        <div class="float-left">
                            نمايش رکوردهای
                            <b>{{$packing_forms->firstItem()}}</b>
                            تا
                            <b>{{$packing_forms->lastItem()}}</b>
                            از
                            <b>{{$packing_forms->total()}}</b>
                            رکورد موجود
                        </div>
                    </div>
                    <div class="text-center">
                        {{$packing_forms->links('pagination::bootstrap-4')}}
                    </div>

                    <a href="{{route("utility.planing.product.dashboard.packing_type_details",$product)}}"
                       class="btn btn-outline-dark">بازگشت</a>
                </div>

            </div>
        </div>

    </div>

@endsection
