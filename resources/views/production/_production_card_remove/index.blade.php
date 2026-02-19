@extends('layouts.admin._master')

@section('page_header_title'," ")

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>جستجوی کارت تولید</h5>
            </div>
            <div class="card-block">
                <form id="form1" action="{{route("search")}}" method="post" novalidate="novalidate">
                    @csrf
                    <div class="row">
                       
                        @include("component.input._text",["id"=>"product_code","lable"=>" سریال  تولید"])
                 

                    </div>
                    
                   
                    
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> جستجو </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
@section("styles")
@include("component.input.datepicker._script")
@endsection
