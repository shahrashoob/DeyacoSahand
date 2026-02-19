
<?php
/**
 * Copyright 2016-2019 Appnitro Software. This code cannot be redistributed without
 * permission from http://www.arjnet.ir/
 * Created by Alireza Jalayegh.
 * Date: 14/05/2019, 04:14 PM
 * Description:
 *
 */
?>
@extends('panel.layouts.master')

@section("head")
    <link rel="stylesheet" href="{{asset('components/minified/themes/default.min.css')}}" id="theme-style"/>
@endsection
@section('content')

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-flat">
                <div class="panel-heading">
                    <div class="col-md-4  ">
                        <h4 class="panel-title">درج تصویر خبر {{$news->caption}} <a class="heading-elements-toggle"><i
                                    class="icon-more"></i></a></h4>
                    </div>
                </div>
                <br>
                <div class="timeline-row">
                    <div class="timeline-icon">
                        <div class="bg-success-400">
                            <i class="icon-users2"></i>
                        </div>
                    </div>
                    <div class="panel panel-flat timeline-content">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                تصویر خبر            </h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                    <li><a data-action="reload"></a></li>
                                    <li><a data-action="close"></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center">
                                    <div class="thumb thumb-slide">
                                        <img src="{{url($news->images->path??"")}}" />
                                    </div>
                                </div>
                            </div>
                            <fieldset class="content-group">
                                <legend class="text-bold"> درج تصویر </legend>
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <strong>اخطار!</strong>مشکلی پیش آمده لطفا در انتخاب فایل دقت کنید.<br><br>
                                        شما مجاز به آپلود انواع فایل های تصویری مي باشيد
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif


                                <form class="form-horizontal" enctype="multipart/form-data" method="post" action="{{url('panel/news/fileUpload/'.$news->id)}}">
                                    {{ csrf_field() }}
                                    <div class="row ">
                                        <div class="col-md-4">
                                            <input type="file" name="image"  class="btn btn-primary"/>
                                            <input type="hidden" name="image_file_id" value="{{$news->id}}"/>
                                            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                                        </div>
                                        <div class="col-md-offset-8 col-md-4"><br/></div>
                                        <div class="col-md-4">
                                            <a href="{{url("panel/report/all_news")}}" class="btn btn-danger">@lang("text.btn.back")</a>
                                            <button type="submit" class="btn btn-primary"><i class="icon icon-upload4"></i> آپلود تصویر </button>


                                        </div>
                                    </div>
                                </form>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{asset('components/minified/sceditor.min.js')}}"></script>
        <script src="{{asset('components/minified/icons/monocons.js')}}"></script>
        <script src="{{asset('components/minified/formats/bbcode.js')}}"></script>
                <!-- / -->
                <script src="https://www.google.com/recaptcha/api.js?render={{config("recaptch.site_key")}}"></script>
                <script>
                    grecaptcha.ready(function () {
                        grecaptcha.execute('{{config("recaptch.site_key")}}', { action: 'contact' }).then(function (token) {
                            var recaptchaResponse = document.getElementById('recaptchaResponse');
                            recaptchaResponse.value = token;
                        });
                    });
                </script>
@endsection





