
@extends('layouts.admin._master')


@section("content")
    <div class="row">

        {{--        @include("component.alert._primary",["content"=>__("page.header.event.add_new")])--}}

        <div class="col-sm-12">
           
            <div class="card">
               @include("component.smartwizard.step",["stepInfo"=>$stepInfo,"active"=>$step])

                <div class="card-block">

                    <div class="col-sm-12">
                     
                        @if($call->warehouse_import_status_id==3340 ||
                            $call->nosa_import_status_id==3340)
                        <div class="alert alert-danger">
                            فایل گزارش نوسا یا لیست موجودی انبار به
                             درستی آپلود نشده است، لطفا دوباره تلاش کنید
                             <div class=""></div>
                        </div>
                            <a href="{{route("call.index",1)}}" class="btn btn-primary">
                                شروع مجدد فراخوانی
                                
                            </a>
                       
                   @else
                    
                   <div class="alert alert-success">
                   فراخوانی جدید با موفقیت در لیست پردازش قرار گرفت
                </div>

                        
                       
                    @endif


                    </div>
                   
                </div>


            </div>

        </div>

    </div>

@endsection
@section("styles")
   
@include("component.smartwizard.script")
@endsection





