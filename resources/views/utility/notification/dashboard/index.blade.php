@extends('layouts.admin._master')
@section("page_header_title","داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"warps.production_form.dashboard.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>  اطلاع رسانی</h5>
                    @if( $post_user->checkButtonPermission("utility.notification.dashboard.create"))

                        <a href="{{route("utility.notification.dashboard.create")}}"
                           class="btn btn-success">
                           ارسال پیامک جدید
                        </a>
                    @endif
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>تعداد پست</th>
                                <th>شناسه یکتای پیامک</th>
                                <th>هزینه پیامک (ریال)</th>
                                <th>وضعیت</th>
                                <th>متن ارسال شده</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->posts->count()}}
                                    </td>
                                    <td>
                                        {{$item->messageid}}
                                    </td>
                                    <td>
                                        {{$item->cost}}
                                    </td>
                                    <td>
                                        {{$item->statustext}}
                                    </td>
                                    <td>
                                        {{$item->message}}
                                    </td>
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
