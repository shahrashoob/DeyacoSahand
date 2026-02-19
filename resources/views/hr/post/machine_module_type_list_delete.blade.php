@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <div class="row">

        <div class="col-md-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>بخش های مورد نیاز جهت تنظیم دسترسی به
                        <b>{{$machine_module_type->caption}}</b>
                        برای
                        <b>{{$post->caption}}</b>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> عنوان</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $status_type_id=>$item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("hr.post.edit_module",[$post,$machine_module_type,$status_type_id])}}">{{$status_type_id}} - {{$item["caption"]}}</a>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <a class="btn btn-outline-dark" href="{{route("hr.post.edit",$post)}}" class="btn btn-outline-defualt">بازگشت</a>


                </div>

            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
