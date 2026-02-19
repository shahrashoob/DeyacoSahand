@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row" style="margin: auto; max-width: 600px">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                </div>
                <div class="card-block" style="text-align: center">
                    <h5> آیا طرح  {{$machine->caption}} را تغییر داده اید؟</h5>


                    <br/>
                    <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}" class="btn btn-lg btn-danger"
                       onclick="return alert('لطفا تنظیمات را انجام دهید.')">خیر</a>


                    <a href="{{route("fabric_raw.jacquard.machine.end_of_change_design.has_yarn_weft_change",$machine)}}"
                       class="btn btn-lg btn-success">بله</a>
                </div>
            </div>
        </div>
    </div>
@endsection
