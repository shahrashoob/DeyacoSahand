@extends('layouts.admin._master')
@section("page_header_title","کارتابل منابع انسانی")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"utility.smart_object.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>ارزیابی عملکرد
                        {{$evaluation_form_export->evaluation_form->worker->fullname()}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <form id="form1"
                              action="{{route('hr.evaluation_form.dashboard.store_indicator',$evaluation_form_export->id)}}"
                              method="post"
                              enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                            @csrf
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th> #</th>
                                    <th>عنوان شاخص</th>
                                    <th style="width: 300px">نمره شاخص (0-100)</th>
                                    <th></th>
                                </tr>

                                </thead>

                                <tbody>
                                @php $row=0;@endphp
                                @foreach($evaluation_form_export->evaluation_form->evaluation_form_indicators as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>{{$item->evaluation_indicator->caption}}</td>
                                        <td>
                                            <input style="width: 60px" type="number" required="required" min="0"
                                                   max="100" name="value_{{ $item->id }}" value="{{$item->value ??""}}">
                                        </td>
                                    </tr>
                                    <tr>

                                        <td colspan="4">
                                            توضیحات:
                                            <input type="text" name="message_id_{{ $item->id }}" style="width: 500px;height:40px" ></input>
                                        </td>

                                    </tr>
                                </tbody>
                                @endforeach
                            </table>
                            <a href="{{route('hr.evaluation_form.dashboard.index')}}" class="btn btn-outline-dark">بازگشت</a>
                            <button type="submit" class="btn btn-primary"> افزودن</button>
                        </form>

                    </div>

                </div>
            </div>

        </div>

        @endsection
        @section("styles")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
        @endsection

        @section("scripts")
            <script>
                $('#form1').validate({
                    rules: {
                        "x": "required",
                        @foreach($evaluation_form_export->evaluation_form->evaluation_form_indicators as $item)
                        "value_{{ $item->id }}": {required: true, min: 0, max: 100},
                        @endforeach
                    }
                });
            </script>
@endsection
