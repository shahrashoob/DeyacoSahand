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
                بافنده گرامی لطفا به کنترل خام جهت خارج کردن پارچه اطلاع داده و
                از ادامه بافت تا زمان استخراج پارچه خودداری فرمایید
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


