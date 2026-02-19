@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن پست جدید  </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("hr.post.insert")}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                                @include("component.input._text",["id"=>"caption"])

                        </div>

                        <a href="{{route("hr.post.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> ثبت پست جدید</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "user_id_auto": "required"
            }
        });
    </script>
@endsection
