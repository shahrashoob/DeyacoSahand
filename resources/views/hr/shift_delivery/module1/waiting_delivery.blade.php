@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <form id="form1" action="{{route( "hr.shift_delivery.module1.submit_waiting_delivery",[$shift_delivery_module,$post])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf

        <div class="row">

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> تایید {{$shift_delivery_module->caption}} برای پست {{$post->caption}}</h5>
                    </div>
                    <div class="card-block ">


                        <div class="row">

                            <div class="alert alert-info md-col-12" style="width: 100%">
                                <h5>بافنده محترم لطفا در صورت تایید کنتور ماشین های زیر بر روی دکمه تایید کلیک فرمایید.</h5>

                            </div>

                            <div class="col-md-3">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach($machine_logs as $machine_log)
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{$machine_log->machine->caption}}</h5>
                        </div>
                        <div class="card-block">


                            <div class="row">

                                <div class=" md-col-12" style="width: 100%">

                                    @for($k=1;$k<=$machine_log->machine->machine_type->get_property_value(6);$k++)
                                        @php  $id="contour_".$k."_value";@endphp
                                        @include("component.input._lable",["id"=>"","label"=>"مقدار ".$machine_log->machine->machine_type->get_property_value(8)." ".$k,"class_col"=>"col-md-12","value"=>$machine_log->$id])
                                    @endfor


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="col-sm-12 center">
                <a class="btn btn-outline-dark"
                   href="{{route("hr.personal.index",[$worker,$worker->random])}}">بازگشت</a>

                <button type="submit" class="btn btn-primary" onclick="return confirm('آیا از تایید تحویل شیفت اطمینان دارید؟')">تایید تحویل شیفت</button>
            </div>
        </div>
    </form>
@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
@section("scripts")

    <script>

    </script>
@endsection
