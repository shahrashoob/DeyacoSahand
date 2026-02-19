@extends('layouts.admin._master')
@section("page_header_title","کارتابل  منابع انسانی ")
@section("content")
    <form id="form1" action="{{route( "hr.shift_delivery.module1.submit",[$shift_delivery_module,$post])}}"
          method="post"
          autocomplete="off"
          novalidate="novalidate">
        @csrf

        <div class="row">

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> {{$shift_delivery_module->caption}} برای پست {{$post->caption}}</h5>
                    </div>
                    <div class="card-block ">


                        <div class="row">

                            <div class="alert alert-info md-col-12" style="width: 100%">
                                <h5> بافنده محترم، ضمن عرض خسته نباشید و خداقوت؛ لطفا کنتور ماشین های زیر را وارد کرده و
                                    بر
                                    روی
                                    دکمه تحویل شیفت کلیک نمایید.</h5>

                            </div>

                            <div class="col-md-3">
                                @include("component.input._select",[
                                    "id"=>"user_id",
                                    "label"=>" همکار شیفت  ",
                                    "option"=>$option,
                                    "class_col"=>""
                                    ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach($machine_list as $machine)
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{$machine->caption}}</h5>
                            <br/>
                            وضعیت تولید:
                            {{$machine->production_status->caption??""}}
                        </div>
                        <div class="card-block">


                            <div class="row">

                                <div class=" md-col-12" style="width: 100%">

                                    @for($k=1;$k<=$machine->machine_type->get_property_value(6);$k++)
                                        @php
                                            $id="machine_".$machine->id."contour_".$k."_value";
                                            $value="";
											if(isset($request[$id])){
												 $value=$request[$id];
											}

                                        @endphp
                                        @include("component.input._number",["id"=>$id,"label"=>"مقدار ".$machine->machine_type->get_property_value(8)." ".$k,"class_col"=>"col-md-12","value"=>$value??""])

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

                <button type="submit" class="btn btn-primary">ثبت تحویل شیفت</button>
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

        $('#form1').validate({
            rules: {
                user_id: "required",

            }
        });

    </script>
@endsection
