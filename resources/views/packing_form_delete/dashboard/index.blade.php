@extends('layouts.admin._master')
@section("page_header_title"," داشبورد بسته بندی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("packing_form.dashboard._search_view",["route"=>"packing_form.dashboard.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست فرم های بسته بندی
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>

                                <th>#</th>
                                <th> شماره فرم بسته بندی</th>
                                <th>شماره فرم انبار</th>
                                <th>نوع بسته بندی</th>
                                <th> شماره حامل</th>
                                <th>مقدار کل</th>
                                <td>وضعیت</td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        @if($item->packing_form_parent)
                                            {{$item->packing_form_parent->getCode()}} =>
                                        @endif
                                        <a href="{{route("packing_form.dashboard.view",$item)}}">
                                            {{$item->getCode()}}
                                        </a>

                                    </td>
                                    <td>
                                        {{$item->form->code??"---"}}
                                    </td>
                                    <td>
                                        {{$item->packing_type->caption??"---"}}
                                    </td>
                                    <td>
                                        {{$item->carrier?$item->carrier->getCaption():""}}
                                    </td>
                                    <td>
                                        {{$item->getAllAmount("final_amount")}}
                                    </td>
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
