@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>سابقه عملیات بر روی ماشین {{$machine->fullCaption()}}
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
                                <th>نوع رویداد</th>
                                <th>وضعیت تولید</th>
                                <th> علت خاموشی</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=1;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}({{$item->id}})</td>
                                    <td>
                                        {{$item->get_datetime()}}
                                    </td>
                                    <td>
                                        {{$item->worker->fullname()}}
                                    </td>
                                    <td>
{{--                                        @if($post_user->checkButtonPermission("fabric_raw.machine.edit_log.index"))--}}
{{--                                           <a href="{{route("fabric_raw.machine.edit_log.index",[$machine,$item])}}">--}}
{{--                                              --}}
{{--                                           </a>--}}
{{--                                        @else--}}

{{--                                        @endif--}}

                                        {{$item->event_type->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->production_status->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->machine_off_reason->caption??""}}
                                    </td>
                                </tr>
                                @if(isset($item->contour_1_value))
                                    <tr class="alert-info">
                                        <td colspan="2">قطب های ماشین:</td>
                                        <td colspan="4">
                                            {{$item->contour_1_value}} -
                                            {{$item->contour_2_value}} -
                                            {{$item->contour_3_value}} -
                                            {{$item->contour_4_value}} -
                                            {{$item->contour_5_value}}
                                            ({{$item->contour_sum_value}})
                                        </td>
                                    </tr>
                                @endif
                                @if($item->message_id !=0)
                                    <tr  class="alert-warning">
                                        <td colspan="6">
                                            {!! $item->message->text??"" !!}
                                        </td>
                                    </tr>
                                    @endif
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
            <a href="{{route("fabric_raw.machine.dashboard.view",$machine)}}" class="btn btn-outline-dark">بازگشت</a>

        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
