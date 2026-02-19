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
                    <h4 class="panel-title">مدیریت دکمه ها : {{$event->name}}<a
                            class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    <hr/>
                </div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <form class="form-horizontal" method="post" >
                            {{ csrf_field() }}
                            <div class="col-md-6 ">
                                @include("component.input._textarea",
                                           ["id"=>"btn_text","label"=>"لطفا محتوای بخش دکمه ها را در اینجا قرار دهید",
                                           "value"=>$event->btn_text])
                            </div>

                            <br/>
                            <br/>

                            <div class="col-md-4  col-md-offset-5">
                                <a href="{{URL::previous()}}" class="btn btn-primary">@lang("text.btn.back")</a>
                                <button type="submit" class="btn btn-success">@lang("text.btn.save")</button>

                            </div>
                        </form>
                    </div>
                    <div class="col-xs-12">
                        نمونه کد آماده:
                        <hr/>
                        <a href="#link1" class="btn btn-primary" >
                            <span class="fa fa-download"></span>نمونه دکمه آبی با آیکن دانلود
                        </a><br/>
                        <br/>
                        <a href="#link2" class="btn btn-success" >
                            <span class="fa fa-download"></span> نمونه دکمه سبز با آیکن دانلود
                        </a>

                        <br/>
                        <br/>
                        <hr/>
@php $html='<a href="#link1" class="btn btn-primary" >
                            <span class="fa fa-download"></span> نمونه دکمه آبی با آیکن دانلود
                        </a><br/>
                        <br/>
                        <a href="#link2" class="btn btn-success" >
                            <span class="fa fa-download"></span> نمونه دکمه سبز با آیکن دانلود
                        </a>

                        <br/>
                        <br/>';@endphp
                       <pre>
                            {{ $html }}
                       </pre>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection
