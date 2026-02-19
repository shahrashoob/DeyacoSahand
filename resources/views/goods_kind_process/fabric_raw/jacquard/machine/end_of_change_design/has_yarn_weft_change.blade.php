@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  بافندگی")

@section('content')
    <div class="row" style="margin: auto; max-width: 600px">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                </div>
                <div class="card-block" style="">
                    <form id="form1"
                          action="{{route("fabric_raw.jacquard.machine.end_of_change_design.submit_yarn_weft_change",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        @foreach($reserve_input_list as $item)
                            @include("component.input._text",["id"=>"input_".$item->id,"label"=>"لات ". $item->material->fullCaption()." - (ورودی ".$item->input_line_code.")","class_col"=>"col-sm-12"])
                        @endforeach

                        <br/>
                        <div class="col-sm-12">
                            <h5> آیا نخ پود {{$machine->caption}} را تغییر داده اید؟</h5>


                            <a href="{{route("fabric_raw.jacquard.machine.dashboard.view",$machine)}}" class="btn btn-lg btn-danger"
                               onclick="return alert('لطفا تنظیمات را انجام دهید.')">خیر</a>


                            <button type="submit" class="btn btn-lg btn-success">بله و ادامه</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
@endsection


        @section("scripts")

            <script>
                $('#form1').validate({
                    rules: {
                        "shift_work_id_auto": "required",
                        @foreach($reserve_input_list as $item)
                        "input_{{$item->id}}": "required",
                        @endforeach
                    }
                });
            </script>
@endsection

