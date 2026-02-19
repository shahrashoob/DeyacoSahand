@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار محصول "." سفارش:".$order->code())

@section('content')

    <div class="row">


        @include("component.input._lable",["id"=>"","lable"=>" شماره فرم ","value"=>$form->code(),"class_col"=>"col-md-6"])
        @include("component.input._lable",["id"=>"","lable"=>" تاریخ  ","value"=>$form->get_create_date(),"class_col"=>"col-md-6"])
        @include("component.input._lable",["id"=>"","lable"=>" کد سفارش  ","value"=>$order->code(),"class_col"=>"col-md-3"])


        @include("component.input._lable",["id"=>"","lable"=>" کانال توزیع ",
                        "value"=>$order->customer->channelType->caption??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" نام مرکز  ",
                        "value"=>$order->customer->caption??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" کد مرکز  ",
                        "value"=>$order->customer->code??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" شرح  ",
                        "value"=>$order->description_sheet->text??"","class_col"=>"col-md-3"])


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
                            <th>انبار</th>
                            <th> کد کالا</th>
                            <th> کد میله ای کالا</th>
                            <th> تعداد کارتن</th>
                            <th>تعداد واحد فرعی<br/> در کارتن</th>
                            <th>  واحد</th>

                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;@endphp
                        @foreach($form->item as $item)
                            <tr>
                                <th>{{$row++}}</th>
                                <th>{{$item->product->warehouse->code??""}} </th>
                                <th>{{$item->product->code}}</th>
                                <th></th>
                                <th>  {{$item->amount}}</th>
                                <th> {{$item->product->number_in_carton}}</th>
                                <th> {{$item->product->unit->bach_caption}}</th>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="text-align: center">
                <a class="btn btn-dark" href="{{route("wh.product.view_order",[$order])}}" >بازگشت</a>
    <a class="btn btn-primary" href="{{route("wh.print.exit_form",[$order,$form])}}" > پرینت درخواست</a>
            </div>
        </div>
    </div>
@endsection
