@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>درخت حساب ها
                        <a class="btn btn-success" href="{{route("accounting.definition.account.create")}}"> <i
                                class="fa fa-plus"></i> افزودن حساب جدید </a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد حساب</th>
                                <th> عنوان حساب</th>
                                <th>زیر حساب ها</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->fullCode()}}
                                    </td>
                                    <td>
                                        <a href="{{route("accounting.definition.account.edit",$item)}}"> {{$item->caption}}</a>
                                    </td>
                                    <td>
                                        <a href="{{route("accounting.definition.account.account_list",$item)}}">
                                         {{$item->items()->count()}} زیر حساب
                                        </a>
                                        <a class="text-success" href="{{route("accounting.definition.account.create_sub_account",$item)}}"> <i class="fa fa-plus-circle"> </i> </a>

                                    </td>
                                    <td>{{$item->status->caption??""}}</td>
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
