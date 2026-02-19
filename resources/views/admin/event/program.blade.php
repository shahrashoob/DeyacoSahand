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
                    <h4 class="panel-title">مدیریت برنامه زمان بندی : {{$event->name}}<a
                            class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    <hr/>
                </div>

                <div class="panel-body">
                    <div class="col-xs-12">
                        <form class="form-horizontal" method="post" >
                            {{ csrf_field() }}
                            <div class="col-md-6 ">
                                @include("component.input._textarea",
                                           ["id"=>"program","label"=>"لطفا محتوای بخش دکمه ها را در اینجا قرار دهید",
                                           "value"=>$event->program,"width"=>600])
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
                        <br/>
                       <h4> تصویر:</h4>
                        <img src="https://idea20.ir/files/event/textile/6/program.jpg" style="width:400px">
                        <br/>
                        <br/>
                        @php $html=' <div class="row">
<img src="#link">
</div>';@endphp
                        <pre>
                            {{ $html }}
                       </pre>

                        <br/>
                        <br/>
                        <br/>
                        <hr/>

<h4>جدول:</h4>

                        <table class="table striped bordered">
                            <thead>
                            <tr>
                                <th style="border-top:none">عنوان برنامه</th>
                                <th style="border-top:none">تاریخ و روز</th>
                                <th style="border-top:none; width: 30px">شروع </th>
                                <th style="border-top:none"></th>
                                <th style="border-top:none; width: 30px">پایان</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td> اولین بخش برنامه
                                    <br>
                                    <span style="font-family: IS"></span>
                                </td>
                                <td>  روز اول</td>
                                <td>  ۱۷:۰۰ </td>
                                <td></td>
                                <td> ۱۷:۳۰ </td>
                            </tr>
                            <tr>
                                <td>دومین بخش برنامه
                                    <br>
                                    <span style="font-family: IS"></span>
                                </td>
                                <td>  روز اول</td>
                                <td>  ۱۷:۳۰ </td>
                                <td></td>
                                <td> ۱۸:۳۰ </td>
                            </tr>
                            <tr>
                                <td> *****

                                </td>                                <td>  روز اول</td>

                                <td>  ۱۸:۳۰ </td>
                                <td></td>
                                <td> ۲۰:۰۰ </td>
                            </tr>

                            </tbody></table>
                        <br/>
                        <hr/>
                        @php $html='<table class="table striped bordered">
                            <thead>
                            <tr>
                                <th style="border-top:none">عنوان برنامه</th>
                                <th style="border-top:none">تاریخ و روز</th>
                                <th style="border-top:none; width: 30px">شروع </th>
                                <th style="border-top:none"></th>
                                <th style="border-top:none; width: 30px">پایان</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td> اولین بخش برنامه
                                    <br>
                                    <span style="font-family: IS"></span>
                                </td>
                                <td>  روز اول</td>
                                <td>  ۱۷:۰۰ </td>
                                <td></td>
                                <td> ۱۷:۳۰ </td>
                            </tr>
                            <tr>
                                <td>دومین بخش برنامه
                                    <br>
                                    <span style="font-family: IS"></span>
                                </td>
                                <td>  روز اول</td>
                                <td>  ۱۷:۳۰ </td>
                                <td></td>
                                <td> ۱۸:۳۰ </td>
                            </tr>
                            <tr>
                                <td> *****

                                </td>                                <td>  روز اول</td>

                                <td>  ۱۸:۳۰ </td>
                                <td></td>
                                <td> ۲۰:۰۰ </td>
                            </tr>

                            </tbody></table>
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
