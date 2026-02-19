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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
@endsection
@section('content')
    @include("component.input.datepicker._script")

    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h4 class="panel-title">افزودن خبر جدید : <a class="heading-elements-toggle"><i
                                class="icon-more"></i></a></h4>
                    <hr/>
                </div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <form class="form-horizontal" method="post" action="{{url('panel/news/add_news/')}}">
                            {{ csrf_field() }}
                            <div class="col-md-4  ">
                                <div class="form-group">
                                    <label for="name">انتخاب رویداد:</label>
                                    <select class="form-control" required name="event_id">
                                        @foreach($event as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8 col-md-offset-8"></div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="name">عنوان خبر:</label>
                                    <input type="text" class="form-control" required name="caption" value=""/>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-offset-8"></div>



                            <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="name">وضعیت خبر:</label>
                                    <select class="form-control" required name="status_id">
                                        @foreach($status as $item)
                                            <option value="{{$item->id}}">{{$item->caption}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            </div>

                            <div class="col-md-8 col-md-offset-8"></div>

                            <div class="col-md-4 ">
                                @include("component.input.datepicker._datepicker",
                                          ["id"=>"register_date","label"=>"تاریخ انتشار خبر",
                                          "value"=>""])
                            </div>

                            <div class="col-md-12">

                                    <div id="editor" style="width: 100%; height: 400px" class="form-control"
                                              required name="description"></div>
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

    <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
{{--    <script src="https://cdn.tiny.cloud/1/bex1l8qp1af9qntay2ceaa3976kximi83t0tqv2a9zyb17po/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>--}}


    <!-- TinyMCE -->
    <script src='https://cloud.tinymce.com/stable/tinymce.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#my-textarea',
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern',
                ],
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media',
                relative_urls: false,
                file_browser_callback: function(field_name, url, type, win) {
                    tinyMCE.activeEditor.windowManager.open({
                        file: '/file-manager/tinymce',
                        title: 'Laravel File Manager',
                        width: window.innerWidth * 0.8,
                        height: window.innerHeight * 0.8,
                        resizable: 'yes',
                        close_previous: 'no',
                    }, {
                        setUrl: function(url) {
                            win.document.getElementById(field_name).value = url;
                        },
                    });
                },
            });
        });
    </script>


    <script src='https://cloud.tinymce.com/stable/tinymce.min.js'></script>
{{--    <script scr="//cdn.tinymce.com/4/tinymce.min.js"></script>--}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#editor',
                directionality:"rtl",
                plugins: 'print preview paste importcss searchreplace' +
                    ' autolink autosave save directionality code visualblocks visualchars ' +
                    'fullscreen image link media template codesample table ' +
                    'charmap hr pagebreak nonbreaking anchor toc ' +
                    ' insertdatetime advlist lists wordcount imagetools ' +
                    'textpattern noneditable help charmap quickbars emoticons',

                imagetools_cors_hosts: ['picsum.photos'],
                menubar: 'file edit view insert format tools table help',
                toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
                toolbar_sticky: true,
                autosave_ask_before_unload: true,
                autosave_interval: '30s',
                autosave_prefix: '{path}{query}-{id}-',
                autosave_restore_when_empty: false,
                autosave_retention: '2m',
                image_advtab: true,
                link_list: [
                    { title: 'My page 1', value: 'http://www.tinymce.com' },
                    { title: 'My page 2', value: 'http://www.moxiecode.com' }
                ],
                image_list: [
                    { title: 'My page 1', value: 'http://www.tinymce.com' },
                    { title: 'My page 2', value: 'http://www.moxiecode.com' }
                ],
                image_class_list: [
                    { title: 'None', value: '' },
                    { title: 'Some class', value: 'class-name' }
                ],
                importcss_append: true,

                relative_urls: false,
                file_browser_callback: function(field_name, url, type, win) {
                    tinyMCE.activeEditor.windowManager.open({
                        file: '/file-manager/tinymce',
                        title: 'Laravel File Manager',
                        width: window.innerWidth * 0.8,
                        height: window.innerHeight * 0.8,
                        resizable: 'yes',
                        close_previous: 'no',
                    },
                        {
                        setUrl: function(url) {
                            win.document.getElementById(field_name).value = url;
                        },
                    });
                },
            });

        });
    </script>


@endsection

