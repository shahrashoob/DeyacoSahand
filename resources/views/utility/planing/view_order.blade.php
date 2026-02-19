@extends('layouts.admin._master',["no_persian"=>1])

@section("page_header_title","کارتابل برنامه ریزی ")
@section("content")

    <div class="row">
        @include("component.input._lable",["id"=>"","lable"=>" کد سفارش  ","value"=>$order->code(),"class_col"=>"col-md-3"])


        @include("component.input._lable",["id"=>"","lable"=>" کانال توزیع ",
                        "value"=>$order->customer->channelType->caption??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" نام مرکز  ",
                        "value"=>$order->customer->caption??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" کد مرکز  ",
                        "value"=>$order->customer->code??"","class_col"=>"col-md-3"])


        @include("component.input._lable",["id"=>"","lable"=>" تاریخ درخواست   ",
                        "value"=>$order->order_date(),"class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" تعداد روز در انتظار    ",
        "value"=>$order->number_of_days_waiting()??"","class_col"=>"col-md-3"])

        @include("component.input._lable",["id"=>"","lable"=>" مجوز خروج   ",
        "value"=>$order->exit_date()??"","class_col"=>"col-md-3"])

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>ثبت فرم خروج از انبار</h5>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-styling" style="font-size: 11px !important">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد کالا</th>
                                <th> عنوان کالا</th>
                                <th>تعداد در کارتن</th>
                                <th> مقدار درخواست</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($order->orderList as $item)
                                <tr>
                                    <td>{{++$row}}</td>

                                    <td>
                                        <a href="{{route("utility.planing.view_order_list",$item)}}">
                                            {{$item->product->code}}
                                        </a>

                                    </td>
                                    <td>{{$item->product->caption}}</td>
                                    <td>{{$item->product->number_in_carton}}</td>
                                    <td>{{$item->carton}} {{$item->product->unit->bach_caption}} </td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
@endsection

