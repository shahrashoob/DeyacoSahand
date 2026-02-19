@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>لیست الگوهای عملیات مالی
                        <a class="btn btn-success"
                           href="{{route("accounting.definition.financial_operation_pattern.create")}}"> <i
                                class="fa fa-plus"></i> افزودن الگوی عملیات مالی </a>
                    </h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> عنوان الگوی عملیات مالی</th>
                                <th>نوع الگوی عملیات مالی</th>
                                <th>حساب های مرتبط اصلی</th>
                                <th>حساب های مرتبط ارزش افزوده</th>
                                <th>حساب های مرتبط تخفیف</th>
                                <th></th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("accounting.definition.financial_operation_pattern.edit",$item)}}"> {{$item->caption}}</a>
                                    </td>
                                    <td>
                                        {{$item->financial_operation_pattern_type->caption}}
                                        ({{$item->financial_operation_pattern_type->financial_credit_or_debit==1?"بدهکار":"بستانکار"}})


                                    </td>
                                    <td>
                                        @foreach($item->main_items as $pattern_item)
                                            {{$pattern_item->account->fullCaption()}}
                                            <a class="" href="{{route("accounting.definition.financial_operation_pattern.remove_item",[$item,$pattern_item])}}" onclick="return confirm('آیا از حذف حساب اطمینان دارید؟')">
                                                 <span class="text-danger"><i class="fa fa-trash"></i></span>
                                            </a>,

                                        @endforeach


                                    </td>

                                    <td>
                                        @foreach($item->tax_items as $pattern_item)
                                            {{$pattern_item->account->fullCaption()}}
                                            <a class="" href="{{route("accounting.definition.financial_operation_pattern.remove_item",[$item,$pattern_item])}}" onclick="return confirm('آیا از حذف حساب اطمینان دارید؟')">
                                                <span class="text-danger"><i class="fa fa-trash"></i></span>
                                            </a>,

                                        @endforeach


                                    </td>

                                    <td>
                                        @foreach($item->off_items as $pattern_item)
                                            {{$pattern_item->account->fullCaption()}}
                                            <a class="" href="{{route("accounting.definition.financial_operation_pattern.remove_item",[$item,$pattern_item])}}" onclick="return confirm('آیا از حذف حساب اطمینان دارید؟')">
                                                <span class="text-danger"><i class="fa fa-trash"></i></span>
                                            </a>,

                                        @endforeach


                                    </td>
                                    <td>
                                        <a class="text-success" href="{{route("accounting.definition.financial_operation_pattern.add_item",$item)}}">
                                            <i class="fa fa-plus-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
