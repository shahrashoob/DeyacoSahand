@extends('layouts.admin._master')
@section("page_header_title","کارتابل منابع انسانی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">

                        <h5>سوابق ارزیابی عملکرد
                            {{$worker->fullname()}}
                        </h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>ارزیاب کننده</th>
                                <th>نوع ارزیابی</th>
                                <th>تاریخ ایجاد</th>
                                <th>امتیاز کسب شده</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                            <tr>
                                <td>{{++$row}}</td>
                                <td>
                                    <a href="{{route('hr.personal.evaluation_form.view',$item->id)}}">{{$item->get_evaluator()}}</a>
                                </td>
                                <td>{{$item->evaluation_type->caption}}</td>
                                <td>{{$item->get_created_at()}}</td>
                                <td>{{$item->value_evaluation_form}}</td>

                            </tr>
                            </tbody>
                            @endforeach
                        </table>
                    </div>
                    <a class="btn btn-outline-dark" href="{{route("hr.personal.index",[$worker->id,$worker->random])}}">بازگشت</a>

                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
