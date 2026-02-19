@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست شاخص های تنظیم شده
                        برای
                        {{$post_evaluation->post->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th> #</th>
                                <th>شاخص</th>
                                <th>نوع ارزیابی</th>
                                <th>وزن شاخص</th>
                                <th>اولویت</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($post_evaluation_indicators as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->evaluation_indicator->caption}}</td>
                                    <td>{{$item->evaluation_type->caption}}</td>
                                    <td>{{$item->weight}}</td>
                                    <td>{{$item->priority_number}}</td>
                                    <td>
                                        <a href="{{route("hr.post.post_evaluation.destroy_indicator",[ $post_evaluation,$item->id])}}"
                                           onclick="return confirm('آیا از حذف این شاخص اطمینان دارید؟')"><i
                                                class="fa fa-trash text-danger"></i> </a>
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
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن شاخص ارزیابی برای
                        {{$post_evaluation->post->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1"
                          action="{{route('hr.post.post_evaluation.store_indicator',$post_evaluation)}}"
                          method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._aotocomplet2",[

                                 "id"=>"evaluation_indicator_id",
                                 "label"=>" شاخص  ",
                                 "option"=>$evaluation_indicator_option["items"],
                                ])
                            @include("component.input._number",["id"=>"weight",'label'=>"وزن شاخص (0-100) ","value"=>""])
                            @include("component.input._number",["id"=>"priority_number",'label'=>"اولویت","value"=>""])

                        </div>
                        <div class="w-100"><br/></div>
                        <a href="{{route('hr.post.post_evaluation.index',$post_evaluation->post_id)}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> افزودن</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "priority_number": "required",
                "weight": "required",
                "evaluation_indicator_id_auto": "required",
            }
        });
    </script>
@endsection
