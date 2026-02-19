@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> لیست قراردادها</h5>
                    <a class="btn btn-outline-success" href="{{route("accounting.contract.contract.create")}}"> <i
                                class="fa fa-plus"></i> افزودن قرارداد جدید </a>
                    <a class="btn btn-outline-primary  " href="{{route("accounting.contract.clause.index")}}"> <i
                                class="fa fa-list"></i> لیست ماده های قراداد </a>

                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th> عنوان</th>
                                <th>نوع قراداد</th>
                                <th>ماده های قراداد</th>
                                <th>وضعیت</th>
                                <th></th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("accounting.contract.contract.edit",$item)}}"> {{$item->caption}}</a>
                                    </td>
                                    <td>{{$item->contract_type->caption}}</td>
                                    <td>
                                        <a href="{{route("accounting.contract.contract.add_clause",$item)}}">
                                            شامل
                                            {{count($item->contract_clause_types()->groupBy('clause_type_id')->get())}}

                                            ماده و
                                            {{$item->contract_clause_types()->count()}}
                                            بند
                                        </a>
                                        <a class="text-success"
                                           href="{{route("accounting.contract.contract.add_clause",$item)}}">
                                            <i class="fa fa-plus-circle"></i>
                                        </a>
                                    </td>
                                    <td>{{$item->active_status->caption}}</td>
                                    <td>
                                        @if($item->active_status_id==1200)

                                            <a href="{{route("accounting.contract.contract.print",$item)}}">
                                                <i class="fa fa-download"></i> دانلود قرارداد</a>
                                        @endif
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

