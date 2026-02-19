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
                    <h4 class="panel-title">ویرایش تاریخ های مهم رويداد : {{$event->name}}<a
                            class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    <hr/>
                </div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <form class="form-horizontal" method="post" >
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$event->id}}">
                            <div class="col-md-6 ">
                                @include("component.input.datepicker._datepicker",
                                           ["id"=>"start_date","label"=>"تاريخ شروع رویداد",
                                           "value"=>$event->start_date])
                            </div>
                            <div class="col-md-6 ">
                                @include("component.input.datepicker._datepicker",
                                           ["id"=>"end_date","label"=>"تاريخ پایان رویداد",
                                           "value"=>$event->end_date])
                            </div>

                            <div class="col-md-6 ">
                                @include("component.input.datepicker._datepicker",
                                           ["id"=>"early_register_date","label"=>"تاريخ شروع ثبت نام",
                                           "value"=>$event->early_register_date])
                            </div>

                            <div class="col-md-6 ">
                                @include("component.input.datepicker._datepicker",
                                           ["id"=>"register_date","label"=>" آخرين زمان ثبت نام",
                                           "value"=>$event->register_date])
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
