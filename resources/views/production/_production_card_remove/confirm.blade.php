@extends('layouts.admin._master')

@section('page_header_title'," ")

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>تایید فرم ارزیابی عملکرد تولید </h5>
            </div>
            <div class="card-block">

                    @php $class_col="col-md-12"; @endphp


                    <div class="row">

                        <div class="col-md-6">

                            @include('production.production_card._info_small')
                        </div>

                        <div class="col-md-6">

                                <div class="row">
                                    @include("component.input._lable",["id"=>"set_up_time","lable"=>" سرپرست تولید","value"=>$production->supervisor_worker->fullname()??""])

                                    @include("component.input._lable",["id"=>"set_up_time","lable"=>"  کد خط","value"=>$production->line->fullCaption()])

                                </div>

                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                @include("component.input._lable",["id"=>"set_up_time","lable"=>" زمان ست آپ  (دقیقه) ","value"=>$production->set_up_time])


                                @include("component.input._lable",["id"=>"unemployment_time","lable"=>" زمان مجاز بی کاری (دقیقه) ","value"=>$production->unemployment_time])

                                @include("component.input._lable",["id"=>"down_time","lable"=>" دون تایم خط (دقیقه) ","value"=>$production->down_time])


                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="row">



                                @include("component.input._lable",["id"=>"number_product","lable"=>"تعداد تولید شده ","value"=>$production->number_product])

                                @include("component.input._lable",["id"=>"sub_number_product","lable"=>" تعداد تکی ","value"=>$production->sub_number_product])

                            </div>
                        </div>


                    </div>




            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card alert alert-info">
            <div class="card-header">
                <h5>لیست شیفت های ثبت شده برای کارت تولید</h5>
            </div>
            <div class="card-block">

                <div class="col-md-12" style="overflow: auto">


                    @include("production.production_card._worker_list")
            </div>


    </div>
    </div>

    <div style="text-align: center">
        <form id="form1" action="{{route("production.submit_confirm",$production)}}" method="post" novalidate="novalidate">
                    @csrf
                <a href="{{route("production.datetime.create",[$production,$production->datetimes->count()])}}" class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-success" onclick="return alert(' آیا از صحت اطلاعات وارد شده اطمینان دارید؟')"> تایید نهایی</button>

        </form>
            </div>
    </div>

</div>

@endsection



