@extends('layouts.admin._master',["keypress_enable"=>1])
@section("page_header_title","داشبورد انبار ")
@php $permission_confirm_packing=$post_user->checkButtonPermission("wh.dashboard.input.confirm_packing");@endphp
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("warehouse.dashboard._search_view",["route"=>"wh.dashboard.index"])
        </div>
        @if($permission_confirm_packing)
            <div class="col-sm-12" id="confirm_packing">
                @include("warehouse.dashboard._confirm_packing",["number_packing_submit"=>0,"checking_product_change_in_entry"=>$checking_product_change_in_entry])
            </div>
        @endif
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست فرم های ورود به انبار</h5>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>

                                <th>کد فرم</th>
                                <th>تاریخ و ساعت</th>
                                <th>حامل</th>
                                <th>کاربر ایجاد کننده</th>
                                <th>انبار</th>
                                <th> وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        <a href="{{route("wh.dashboard.show_form",[$item,$list->currentPage()])}}">{{$item->getCode()}}</a>
                                    </td>
                                    <td>
                                        {{$item->get_create_date_and_time()}}
                                    </td>
                                    <td>{{$item->status_id ==500000200 ?$item->getCarrierCation():"---"}}</td>
                                    <td>
                                        <a href="{{route("wh.dashboard.show_form",$item)}}">{{$item->worker->fullname()}}</a>
                                    </td>
                                    <td>{{$item->warehouse->caption??""}}</td>
                                    <td>{{$item->status->caption??""}}</td>


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
