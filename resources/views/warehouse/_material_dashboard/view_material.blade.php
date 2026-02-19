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
                    <h5>
                        کارت های درخواست شده:
                        @foreach($rfw_production_list as $item)
                            {{$item->production->serial()}},
                        @endforeach
                    </h5>
                    <br/>
                    <br/>

                    <form id="form1" action="{{route("wh.material.confirm_form_request",$production)}}" method="post"
                          novalidate="novalidate">
                        @csrf

                        @include("warehouse.material_dashboard._rfw_list",["production"=>$production,"allow_register_new_form"=>$allow_register_new_form])


                        <a href="{{route("wh.material.list")}}" class="btn btn-outline-dark">بازگشت</a>

                        <a href="{{route("wh.print.print_rfw_group",$production)}}" class="btn btn-info">پرینت
                            درخواست</a>

                        @if($allow_register_new_form)
                            <a href="{{route("wh.material.view_materials",[$production->id,1])}}" class="btn btn-info">
                                تحویل به
                                مقدار درخواست</a>

                            <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>
                        @else
                            <div class="w-100"><br/></div>

                            <div class="alert alert-warning"  style="font-size: 18px" >
                                <i class="fa fa-exclamation-triangle fa-2x " > </i>
                                حداقل یک فرم در انتظار تایید (تولید) وجود دارد، پس از تایید فرم می توانید فرم جدید ثبت نمایید
                            </div>
                        @endif
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

                    @include("warehouse.material_dashboard._form_list",["production"=>$production])


                </div>
            </div>
        </div>
    </div>

@endsection
