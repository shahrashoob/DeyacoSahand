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
        <div class="col-xs-12 col-md-9">
            <!-- Timeline -->
            <div class="timeline timeline-left content-group">
                <div class="timeline-container">

                    <!-- basic info -->
                @include('panel.event.basicinfo')
                <!--  basic info -->

                    <!-- supporters  -->
                @include('panel.event.supporters')
                <!--  supporters -->

                    <!-- supporters  -->
                @include('panel.event.images')
                <!--  supporters -->


                </div>
            </div>
            <!-- /timeline -->

        </div>

        <div class="col-xs-12 col-md-3">
            <div class="thumbnail">
                <div class="thumb thumb-slide">
                    <img src="{{url($event->logo->path??"")}}"/>
                </div>


                <div class="caption text-center ">
                    <h6 class="text-semibold no-margin">{{$event->name}} </h6>
                    <h6 class="text-semibold no-margin">
                        <a href="{{$event->getUrl()}}" target="_blank">{{$event->getUrl()}}</a>
                        <small class="display-block"> {{$event->event_type->caption}}</small>
                    </h6>

                </div>
                <div>
                    <a type="submit" class=" list-group-item " data-toggle="modal" data-target="#modal_add_to_wallet">
                        <i class="icon icon-calendar"></i> شروع رويداد: {{$event->start_date()}}
                    </a>
                    <a type="submit" class=" list-group-item " data-toggle="modal"
                       data-target="#modal_minus_from_wallet">
                        <i class="icon icon-calendar"></i> پایان رويداد: {{$event->end_date()}}
                    </a>

                    <a type="submit" class=" list-group-item " data-toggle="modal"
                       data-target="#modal_minus_from_wallet">
                        <i class="icon icon-calendar"></i> شروع ثبت نام: {{$event->early_register_date()}}
                    </a>

                    <a type="submit" class=" list-group-item " data-toggle="modal"
                       data-target="#modal_minus_from_wallet">
                        <i class="icon icon-calendar"></i> آخرين زمان ثبت نام: {{$event->register_date()}}
                    </a>

                    <div class="caption text-center text-danger ">
                        <h6 class="text-semibold no-margin">
                            نمایش رویداد در وب سایت:

                            <a href="{{url("panel/event/change_status/".$event->id)}}/2" >
                                <i class="icon icon-{{$event->status_id!=1?"check":""}}"></i>
                                بله
                            </a>
                            &nbsp;&nbsp;
                            <a href="{{url("panel/event/change_status/".$event->id)}}/1" >
                                <i class="icon icon-{{$event->status_id==1?"check":""}}"></i>
                                خیر
                            </a>

                        </h6>
                    </div>

                    <div class="caption text-center ">
                        <h6 class="text-semibold no-margin">
                            <a href="{{url("panel/event/datetime/".$event->id)}}" >
                                <i class="icon icon-pencil"></i>
                                ویرایش تاریخ های رویداد
                            </a>
                        </h6>

                    </div>
                    <div class="caption text-center ">
                        <h6 class="text-semibold no-margin">
                            <a href="{{url("panel/event/contact/".$event->id)}}" >
                                <i class="icon icon-pencil"></i>
                                ویرایش اطلاعات تماس
                            </a>
                        </h6>

                    </div>

                    <div class="caption text-center ">
                        <h6 class="text-semibold no-margin">
                            <a href="{{url("panel/event/buttons/".$event->id)}}" >
                                <i class="icon icon-pencil"></i>
                                مدیریت دکمه ها
                            </a>
                        </h6>

                    </div>
                    <div class="caption text-center ">
                        <h6 class="text-semibold no-margin">
                            <a href="{{url("panel/event/color/".$event->id)}}" >
                                <i class="icon icon-pencil"></i>
                                ویرایش رنگ بندی صفحه
                            </a>
                        </h6>

                    </div>
                    <div class="caption text-center ">
                        <h6 class="text-semibold no-margin">
                            <a href="{{url("panel/event/program/".$event->id)}}" >
                                <i class="icon icon-pencil"></i>
مدیریت برنامه زمانبندی                            </a>
                        </h6>

                    </div>



                </div>
            </div>


            <div class="alert alert-info alert-block text-bold">
                وضعيت رویداد : {{ $event->status->caption }}

            </div>


            <div class="text-center">
                <br/><br/>
                <a href="{{url("panel/report/my_events") }}" class="btn btn-primary">@lang('text.btn.back') <i
                        class="icon-arrow-left13 position-right"></i></a>

            </div>

        </div>
    </div>

@endsection
