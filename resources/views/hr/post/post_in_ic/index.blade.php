@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>
                        بروزرسانی برای پست
                        {{$post->caption}}
                        از منظومه داده ای
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("hr.post.post_in_ic.search",$post)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"search",'label'=>"متن جستجو ","value"=>$search])

                        </div>

                        <a href="{{route("hr.post.edit",$post)}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> جستجو</button>

                    </form>
                    <br/>
                    @if ($search!="")
                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th> کد پست</th>
                                    <th> عنوان</th>
                                    <th>  بروزرسانی از منظومه داده ای</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=1;@endphp
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            {{$item["id"]}}
                                        </td>
                                        <td>
                                            {{$item["caption"]}}
                                        </td>
                                        <td>
                                            <a  href="{{route("hr.post.post_in_ic.update",[$post,$item["id"]])}}">
                                                <i class="fa fa-undo"></i>
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
{{--@section("scripts")--}}
{{--    <script>--}}
{{--        $('#form1').validate({--}}
{{--            rules: {--}}
{{--                "search": "required",--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}
{{--@endsection--}}