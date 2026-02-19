@extends('layouts.admin._master')

@section("page_header_title"," داشبورد تولید - بافندگی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم نت {{$maintenance->getCode()}}</h5>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12" style="font-size: 16px;">
                            {!! $maintenance->description !!}
                        </div>
                        <div class="w-100"><br/><br/></div>
                        @include("component.input._lable",["id"=>"code",'label'=>" ماشین","value"=>$maintenance->machine->fullCaption(),"readonly"=>1])
                        @include("component.input._lable",["id"=>"caption",'label'=>"نوع عملیات نت ","value"=>$maintenance->maintenance_type->caption,"autofocus"=>1])
                        @include("component.input._lable",["id"=>"lot_effective_code",'label'=>"تاریخ درخواست","value"=>$maintenance->get_create_date_and_time()])
                        @include("component.input._lable",["id"=>"lot_effective_code",'label'=>"وضعیت","value"=>$maintenance->status->caption])


                    </div>
                    <a href="{{route($route_path."index")}}" class="btn btn-outline-dark">بازگشت</a>

                    @if($maintenance->status_id==6003001)
                        <form id="form1" action="{{route($route_path."submit_start",$maintenance)}}" style="display: inline"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('آیا از شروع عملیات اطمینان دارید؟')">شروع عملیات
                            </button>
                        </form>
                    @elseif($maintenance->status_id==6003002)
                        <form id="form1" action="{{route($route_path."submit_end",$maintenance)}}" style="display: inline"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('آیا از پایان عملیات اطمینان دارید؟')">پایان عملیات
                            </button>
                        </form>

                    @elseif($maintenance->status_id==6003004)
                        <form id="form1" action="{{route($route_path."confirm_maintenance",$maintenance)}}" style="display: inline"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('آیا از تایید عملیات اطمینان دارید؟')">تایید نت
                            </button>
                        </form>
                        <form id="form2" action="{{route($route_path."reject_maintenance",$maintenance)}}" style="display: inline"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('آیا از تایید عملیات اطمینان دارید؟')">عدم تایید نت
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>

        @include("line_product_station.maintenance.dashboard._log")

    </div>

@endsection

@section("scripts")

@endsection
