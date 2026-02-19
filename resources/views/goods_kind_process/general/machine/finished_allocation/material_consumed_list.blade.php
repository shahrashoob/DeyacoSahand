@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>سابقه ثبت تراکنش مصرف برای تخصیص {{$allocation->id}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>زمان ثبت تراکنش</th>
                                <th>کنتور شروع</th>
                                <th>کنتور پایان</th>
                                <th>رویداد ماشین</th>
                                <th> برگ خروج مصرف</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=$list->firstItem()-1;@endphp
                            @foreach($list as $item)
                                <tr @if($item->status_id ==6020005 ) style="background:#ef9c9c " @endif>
                                    <td title="{{$item->id}}">{{++$row}}</td>
                                    <td>
                                        {{$item->get_updated_date()}}
                                    </td>
                                    <td>
                                        {{$item->start_machine_log->contour_sum_value??""}}
                                    </td>
                                    <td>
                                        {{$item->end_machine_log->contour_sum_value??""}}
                                    </td>
                                    <td>
                                        {{$item->end_machine_log->event_type->caption??""}}
                                    </td>
                                    <td>

                                        @foreach($item->forms as $form)
                                            <a target="_blank"
                                               href="{{route("DCEF_QR",[$form->id,$form->random])}}">برگ
                                                خروج {{$form->code}}</a>,
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
        <div class="col-md-12 center">
            <a href="{{route($route_path."index",$machine)}}"
               class="btn btn-outline-dark">بازگشت</a>

        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
