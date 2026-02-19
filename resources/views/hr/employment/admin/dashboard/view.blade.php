@extends('layouts.admin._master')
@section("page_header_title","کارتابل همکاری با ما")
@section("content")

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">


                    <h5>اطلاعات
                        <span>{{$employment->worker->firstname}} {{$employment->worker->lastname}}</span>

                    </h5>

                </div>
                @include("hr.employment.admin.dashboard._tab")


                <br/>
                @include("hr.employment.admin.dashboard._action")

            </div>
        </div>
    </div>






    <div class="col-md-12">
        <a href="{{route('hr.employment.admin.dashboard.index')}}"
           class="btn btn btn-outline-dark ">بازگشت</a>
    </div>

@endsection

@include('hr.employment.admin.dashboard._modal')


@section("scripts")
    @include("component.modal.md-modal._script")

    <script>
        $('#form-15').validate({
            rules: {
                "message": "required"
            }
        });
        $('#form-19').validate({
            rules: {
                "message": "required"
            }
        });
        $('#form-10').validate({
            rules: {
                "message": "required"
            }

        });
        $('#form-16').validate({
            rules: {
                "message": "required"
            }

        });
        $('#form-13').validate({
            rules: {
                "message": "required"
            }

        });
        $('#form-17').validate({
            rules: {
                "message": "required"
            }

        });
    </script>

@endsection

@section("styles")
    @include("component.modal.md-modal._style")
@endsection

@section("styles")
    <script
            src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet"
          href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
