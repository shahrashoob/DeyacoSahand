@extends('layouts.admin._master')
@section("page_header_title","داشبورد مجوز های بارگیری")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست مجوز های بارگیری </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره مجوز</th>
                                <th>تاریخ ایجاد</th>
                                <th>وضعیت</th>
                                <th>درخواست های خروج از انبار</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>

                                    <td>{{$item->code}}</td>
                                    <td>{{$item->create_datetime()}}</td>
                                    <td>{{$item->status->caption}}</td>
                                    <td>
                                        @foreach($item->items as $permission_item)
                                            <a href="{{route("wh.out.dashboard.view",$permission_item->product_request_form->id??0)}}">{{$permission_item->product_request_form->code??""}}</a> ,
                                        @endforeach

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

