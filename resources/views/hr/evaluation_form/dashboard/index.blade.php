@extends('layouts.admin._master')
@section("page_header_title","کارتابل منابع انسانی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>ارزیابی عملکرد پرسنل
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>نام و نام خانوادگی</th>
                                <th>تاریخ ایجاد فرم ارزیابی</th>
                                <th>آخرین مهلت انجام فرم ارزیابی</th>
                                <th> نوع ارزیابی</th>
                                <th> وضعیت ارزیابی</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td><a href="{{route('hr.evaluation_form.dashboard.confirm_indicator',$item->id)}}">
                                            {{$item->evaluation_form->worker?$item->evaluation_form->worker->fullname():("**".$item->evaluation_form->worker_id)}}</a></td>
                                    <td>{{$item->evaluation_form->get_created_at()??""}}</td>
                                    <td>{{$item->evaluation_form->get_last_completion_date_time()??""}}</td>
                                    <td>{{$item->evaluation_form->evaluation_type->caption??""}}</td>
                                    <td>{{$item->evaluation_form->status->caption??""}}</td>
                                </tr>

                            </tbody>
                            @endforeach
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
