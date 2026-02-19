@extends('layouts.admin._master')
@section("page_header_title"," داشبورد برنامه ریزی - لیست کالاها ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>موجودی
                    <b>{{$product->fullCaption()}}</b>
                        به تفکیک بسته بندی
                    </h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد نوع بسته بندی</th>
                                <th>نوع بسته بندی</th>
                                <th> مقدار</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($packing_type_list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        {{$item->code}}
                                    </td>
                                    <td>
                                        <a href="{{route("utility.planing.product.dashboard.packing_form_details",[$product,$item])}}">
                                        {{$item->caption}}
                                        </a>

                                    </td>

                                    <td>{{$inventory[$item->id]}}</td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                    <a href="{{route("utility.planing.product.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>
                </div>

            </div>
        </div>

    </div>

@endsection
