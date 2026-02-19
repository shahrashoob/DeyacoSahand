@extends('layouts.admin._master')
@section("page_header_title"," داشبورد بسته بندی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("goods_kind_process.fabric_raw.packing_form._search_view",["route"=>"fabric_raw.packing_form.index"])
            <div class="card">
                <div class="card-header">
                    <h5> لیست  بسته بندی ها
                    </h5>
                    <a href="{{route("fabric_raw.packing_form.page_printing.index")}}">
                        <i class="fa fa-print"></i> پرینت بسته بندی ها
                    </a>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>

                                <th>ردیف</th>
                                <th>تاریخ ایجاد بسته</th>
                                <th> شماره  بسته بندی</th>
                                <th>شماره فرم ورود به انبار</th>
                                <th>نوع بسته بندی</th>
                                <th> شماره حامل</th>
                                <th>مقدار کل</th>
                                <th>درصد جمع شدگی</th>
                                <th>وضعیت</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>
                                        {{$item->get_create_date_and_time()}}
                                    </td>
                                    <td>
                                        @if($item->packing_form_parent)
                                            <a id="dcpk{{$item->id}}" class="text-dark"
                                               href="{{route("fabric_raw.packing_form.view",$item->packing_form_parent)}}/{{$list->currentPage()}}">
                                                {{$item->packing_form_parent->getCode()}}
                                            </a>
                                            =>
                                        @endif
                                        <a id="dcpk{{$item->id}}"
                                           href="{{route("fabric_raw.packing_form.view",$item)}}/{{$list->currentPage()}}">
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
                                    <td>
                                        {{$item->final_shrinkage_percent()." %"}}
                                    </td>
                                    <td>{{$item->getStatus()}}</td>


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
