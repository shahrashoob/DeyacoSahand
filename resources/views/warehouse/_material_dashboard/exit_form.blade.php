@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار مواد اولیه "." کارت تولید :".$production->serial())

@section('content')

    <div class="row">


        @include("component.input._lable",["id"=>"","lable"=>" شماره فرم ","value"=>$form->code(),"class_col"=>"col-md-6"])
        @include("component.input._lable",["id"=>"","lable"=>" تاریخ  ","value"=>$form->get_create_date(),"class_col"=>"col-md-6"])
        @include("component.input._lable",["id"=>"","lable"=>" کارت تولید","value"=>$production->serial(),"class_col"=>"col-md-3"])



    </div>

    <div class="row">


        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>ردیف های محصول</h5>
                </div>
                <div class="card-block">
                    <table class="table table-styling">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th> کد کالا</th>
                            <th> نام کالا </th>
                            <th> واحد سنجش</th>
                            <th> مقدار</th>

                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($form->item as $item)
                            <tr>
                                <th>{{$row++}}</th>
                                <th>{{$item->product->code}}</th>
                                <th>{{$item->product->caption}}</th>
                                <th> {{$item->product->unit->caption}}</th>
                                <th>  {{$item->amount}}</th>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="text-align: center">
                <a class="btn btn-dark" href="{{route("wh.material.view_materials",[$production])}}" >بازگشت</a>
    <a class="btn btn-primary" href="{{route("wh.print.form",$form)}}" > پرینت درخواست</a>
            </div>
        </div>
    </div>
@endsection
