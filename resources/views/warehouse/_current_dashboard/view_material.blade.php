@extends('layouts.admin._master')

@section('page_header_title'," درخواست کالا از انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> درخواست کالا از انبار : {{$production->serial()}}</h5>
                </div>
                <div class="card-block">

                    <div class="row">
                        @include("component.input._lable",["id"=>"","lable"=>"   تاریخ درخواست","value"=>$production->get_create_date()])
                        @include("component.input._lable",["id"=>"","lable"=>"   شماره درخواست ","value"=>$production->serial()])
                        @include("component.input._lable",["id"=>"","lable"=>"    عنوان مرکز هزینه ","value"=>$production->product->ic])

                    </div>


                    <form id="form1" action="{{route("wh.cd.confirm_form_request",$production)}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        @include("warehouse.current_dashboard._rfw_list",["production"=>$production])


                        <a href="{{route("wh.cd.list")}}" class="btn btn-outline-dark">بازگشت</a>

                        <a href="{{route("wh.print.print_rfw",$production)}}" class="btn btn-info">پرینت درخواست</a>

                        <a href="{{route("wh.cd.delivery_form_request",$production)}}" class="btn btn-info"> تحویل به
                            مقدار درخواست</a>

                        <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>

                    </form>


                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> سوابق ثبت درخواست</h5>
                </div>
                <div class="card-block">

                    @include("warehouse.current_dashboard._form_list",["production"=>$production])


                </div>
            </div>
        </div>
    </div>

@endsection
