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
    @include("component.input.datepicker._script")

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-flat">
                <div class="panel-heading">

                    <div class="col-md-4  ">
                        <h4 class="panel-title">ویرایش خبر {{$news->caption}} <a class="heading-elements-toggle"><i
                                    class="icon-more"></i></a></h4>
                    </div>

                    <div class="col-md-1 ">
                        <form method="post" action="{{asset('panel/news/delete/'.$news->id)}}" id="form-delete"
                              role="form">
                            {{csrf_field()}}
                            <input name="_method" value="DELETE" type="hidden">
                            <button type="submit" class="btn btn-danger"><i class="icon icon-trash"></i>حذف خبر</button>
                        </form>
                    </div>
                    <div class="col-md-1 ">
                        <form method="post" action="{{asset('panel/news/delete_file/'.$news->id)}}" id="delete_picture"
                              role="form">
                            {{csrf_field()}}
                            <input name="_method" value="DELETE" type="hidden">
                            <button type="submit" class="btn btn-danger"><i class="icon icon-trash"></i> حذف تصویر خبر
                            </button>
                        </form>
                    </div>
                    <br>

                    <hr/>
                </div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <form method="post" action="{{url('panel/news/edit')}}/{{$news->id}}" role="form">
                            <input type="hidden" name="_method" value="PUT">
                            {{ csrf_field() }}
                            <div class="col-md-4  ">
                                <div class="form-group">
                                    <label for="name"> رویداد:</label>
                                    <select class="form-control" required name="event_id">
                                        <option value="{{$news->event_id}}" selected>{{$news->event->name}}</option>
                                        @foreach($event as $item)
                                            @if($item->id != $news->events_id)
                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8 col-md-offset-8"></div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="name">عنوان خبر:</label>
                                    <input type="text" class="form-control" required name="caption"
                                           value="{{$news->caption}}"/>
                                </div>
                            </div>

                            <div class="col-md-8 col-md-offset-8"></div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="name">وضعیت خبر:</label>

                                    <select class="form-control" required name="status_id">
                                        <option value="{{$news->status_id}}"
                                                selected>{{$news->status->caption}}</option>
                                        @foreach($status as $item)
                                            @if($item->id != $news->status_id)
                                                <option value="{{$item->id}}">{{$item->caption}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8 col-md-offset-8"></div>

                            <div class="col-md-4 ">


                                    @include("component.input.datepicker._datepicker",
                                           ["id"=>"register_date","label"=>"تاریخ انتشار خبر",
                                           "value"=>$news->register_date])

                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">متن خبر :</label>
                                    <textarea id="description" style="width: 100%; height: 400px" class="form-control"
                                              required name="description">{{$news->description}}</textarea>
                                </div>
                            </div>
                            <br/>
                            <br/>

                            <div class="col-md-4  col-md-offset-5">
                                <a href="{{url("panel/report/all_news")}}" class="btn btn-danger">@lang("text.btn.back")</a>
                                <button type="submit" class="btn btn-primary"><i
                                        class="icon icon-pencil"></i>@lang("text.btn.edit")</button>
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
        $("#form-delete").submit(function () {
            var result = confirm("آیا از حذف اطمینان دارید؟");
            return result;
        });
        $("#delete_picture").submit(function () {
            var result = confirm("آیا از حذف تصویر اطمینان دارید؟");
            return result;
        });
    </script>

@endsection

