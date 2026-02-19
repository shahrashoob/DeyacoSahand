@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>سابقه عملیات بر روی  ورودی  {{$current_machine_input->input_line_code}} - {{$machine->fullCaption()}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>
                                <td>ردیف</td>
                                <th>تاریخ و ساعت</th>
                                <th>اقدام کننده</th>
                                <th>کارت تولید</th>
                                <th>کالا</th>
                                <th>لات</th>
                                <th>بسته بندی وارد شده</th>
                                <th>بسته بندی انتخاب شده <br/>توسط دستیار دیجیتال</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem();@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td title="{{$item->id}}">  {{$row++}}</td>
                                    <td>
                                        {{$item->get_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->worker->fullname()}}
                                    </td>
                                    <td>
                                        {{$item->production->serial??""}}
                                    </td>
                                    <td>
                                        {{$item->material->fullCaption()}}
                                    </td>
                                    <td>
                                        {{$item->lot_number->code}}
                                    </td>
                                    <td>
                                        {{$item->entry_packing_form->code??""}}
                                    </td>
                                    <td>
                                        {{$item->packing_form->code??""}}
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
        <div class="col-md-12 center">
            <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>

        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
