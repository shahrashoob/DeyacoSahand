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
                    <h4 class="panel-title">ویرایش اطلاعات تماس رویداد : {{$event->name}}<a
                            class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    <hr/>
                </div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <form class="form-horizontal" method="post" >
                            {{ csrf_field() }}
                            <div class="col-md-6 ">
                                @include("component.input._text",
                                           ["id"=>"tell","label"=>"تلفن",
                                           "value"=>$event->tell])
                            </div>
                            <div class="col-md-6 ">
                                @include("component.input._text",
                                           ["id"=>"email","label"=>"ایمیل",
                                           "value"=>$event->email])
                            </div>

                            <div class="col-md-6 ">
                                @include("component.input._text",
                                           ["id"=>"telegram","label"=>"شناسه تلگرام",
                                           "value"=>$event->telegram])
                            </div>


                            <div class="col-md-6 ">
                                @include("component.input._text",
                                           ["id"=>"instagram","label"=>"شناسه اینستاگرام",
                                           "value"=>$event->instagram])
                            </div>

                            <div class="col-md-6 ">
                                @include("component.input._text",
                                           ["id"=>"location","label"=>"آدرس محل برگذاری",
                                           "value"=>$event->location])
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
