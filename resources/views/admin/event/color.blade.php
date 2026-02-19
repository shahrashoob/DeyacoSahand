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
                    <h4 class="panel-title">ویرایش رنگبندی صفحه رویداد : {{$event->name}}<a
                            class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    <hr/>
                </div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <form class="form-horizontal" method="post" >
                            {{ csrf_field() }}
                            <div class="col-md-6 ">
                                @include("component.input._text",
                                           ["id"=>"color_nav","label"=>"رنگ هدر ( بعد از اسکرول)",
                                           "value"=>$event->color_nav])
                            </div>
                            <div class="col-md-6 ">
                                @include("component.input._text",
                                           ["id"=>"color_footer","label"=>"رنگ فوتر",
                                           "value"=>$event->color_footer])
                            </div>

                            <div class="col-md-6 ">
                                @include("component.input._text",
                                           ["id"=>"color_tem1","label"=>"رنگ اصلی قالب",
                                           "value"=>$event->color_tem1])
                            </div>

                            <div class="col-md-6 ">
                                @include("component.input._text",
                                           ["id"=>"color_tem2","label"=>"رنگ دوم قالب",
                                           "value"=>$event->color_tem2])
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


@endsection
