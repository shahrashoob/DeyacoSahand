@extends('layouts.admin._master')
@section("page_header_title","کارتابل منابع انسانی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات ارزیابی عملکرد برای
                        {{$post->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th> #</th>
                                <th> نوع ارزیابی</th>
                                <th> وضعیت</th>
                                <th>دوره ارزیابی</th>
                                <th>آخرین زمان ارزیابی</th>
                                <th>زمان بعدی انجام ارزیابی</th>
                                <th>شاخص های ارزیابی</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($evaluation_type_list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("hr.post.post_evaluation.edit",[$post,$item->id])}}"> {{$item->caption}}</a>
                                    </td>
                                    @if($post_evaluation_list->has($item->id))
                                        <td>{{$post_evaluation_list[$item->id]->active_status->caption??""}}</td>
                                        <td> {{$post_evaluation_list[$item->id]->cron}}</td>
                                        <td> {{$post_evaluation_list[$item->id]->get_last_run_date_time()}}</td>
                                        <td>{{$post_evaluation_list[$item->id]->get_next_must_run_date_time()}}</td>
                                        <td>
                                            <a href="{{route("hr.post.post_evaluation.add_indicator",$post_evaluation_list[$item->id])}}">
                                                {{$post_evaluation_list[$item->id]->post_evaluation_indicators()->count()}}
                                                شاخص ارزیابی</a>
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>
                </div>

            </div>


        </div>

        @endsection
        @section("styles")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
