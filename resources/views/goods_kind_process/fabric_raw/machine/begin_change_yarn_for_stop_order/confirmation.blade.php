@extends('layouts.admin._master')

@section('page_header_title',"داشبورد جاری تولید -  ")

@section('content')
    <div class="row">
        <div class="col-sm-12 center">

            <br/>
            <br/>
            <br/>
            <h4>  ماشین <b>{{ $machine->fullCaption() }} </b>            </h4>
            <div class="alert alert-danger" style="font-size: 18px">
                بافنده گرامی لطفا شروع به بافت نمایید
                و پس از اینکه تغییر لات (همبافت) به چروک گیر ماشین رسید دکمه
                پایان تغییر نخ پود را زده و به کنترل خام جهت استخراج پارچه اطلاع دهید
            </div>


            <br/>
            <br/>
            <br/>
            <a href="{{route("fabric_raw.machine.dashboard.view",$machine)}}" class="btn btn-primary ">بازگشت</a>


            <br/>
            <br/>

        </div>


    </div>

@endsection


