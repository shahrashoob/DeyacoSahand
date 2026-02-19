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
                    <h4 class="panel-title">افزودن رويداد جدید : <a class="heading-elements-toggle"><i
                                class="icon-more"></i></a></h4>
                    <hr/>
                </div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <form class="form-horizontal" method="post" action="{{url('panel/event/add/')}}">
                            {{ csrf_field() }}
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="name">عنوان رویداد:</label>
                                    <input type="text" class="form-control" required name="name" value=""/>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-8"></div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="name">شناسه یکتا (انگلیسی / بدون فاصله / حداقل 5 حرف یا عدد):</label>
                                    <input type="text" class="form-control" required name="key" pattern="[a-z1-9]{5,}"
                                           value=""/>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-8"></div>
                            <div class="col-md-4  ">
                                <div class="form-group">
                                    <label for="name">نوع رویداد:</label>
                                    <select class="form-control" required name="event_type_id">
                                        {!! $option !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">توضیحات :</label>
                                    <textarea id="description" style="width: 100%; height: 400px" class="form-control"
                                              required name="description"></textarea>
                                </div>
                            </div>
                            <br/>
                            <br/>

                            <div class="col-md-4  col-md-offset-5">
                                <a href="{{URL::previous()}}" class="btn btn-primary">@lang("text.btn.back")</a>
                                <button type="submit" class="btn btn-success">@lang("text.btn.save")</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="{{asset('components/minified/sceditor.min.js')}}"></script>
    <script src="{{asset('components/minified/icons/monocons.js')}}"></script>
    <script src="{{asset('components/minified/formats/bbcode.js')}}"></script>
    <script>
        var toolbar = 'bold,italic,underline,strike|subscript,superscript|left,center,right,justify|font,size,color,removeformat|cut,copy,paste,pastetext|bulletlist,orderedlist|table,code,quote,horizontalrule|email,link,unlink|date,time|ltr,rtl|print,maximize,source';
        var textarea1 = document.getElementById('description');
        sceditor.create(textarea1, {
            format: 'xhtml',
            toolbar: toolbar
        });
    </script>

@endsection
