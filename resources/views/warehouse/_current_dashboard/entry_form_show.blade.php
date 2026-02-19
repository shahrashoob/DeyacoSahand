@extends('layouts.admin._master')

@section('page_header_title'," داشبورد انبار ")

@section('content')
    <div class="row">



        <div class="col-sm-12" style="text-align: center">
            <br/><br/>
            <br/>
            <h4 class="text-success text-big">فرم با موفقیت ثبت شد.</h4>
            <br/> <br/>
            <br/> <br/>
            <a href="{{route("wh.entry_form_list")}}" class="btn btn-primary">بازگشت به لیست فرم ها</a>
            <a href="{{route("wh.entry_form_to_warehouse",$form)}}" class="btn btn-success"> <i class="fa fa-print"></i>  ثبت فرم جدید </a>
            <a href="{{route("wh.print.form",$form)}}" class="btn btn-info"> <i class="fa fa-print"></i> پرینت فرم </a>
        </div>


    </div>
@endsection
