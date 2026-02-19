@extends('layouts.admin._master',["no_persian"=>1])


@section("content")



    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  ثبت اطلاعات شیفت و افراد شاغل  - مرحله {{$version}} </h5>
                </div>
                <div class="card-block">
                    <form id="form1" autocomplete="off" action="{{route("production.datetime.store",[$production,$version,$action])}}" method="post" novalidate="novalidate">
                        @csrf

                        @include('production.production_card._info_small')


                        <div class="row">


                        @include("component.input.datepicker._datepicker",["id"=>"production_date","lable"=>" تاریخ ","value"=>$production_date_time->start_datetime])

                        @include("component.input._time",["id"=>"shift_time","lable"=>" زمان شیفت ",
                                     "h"=>$shift_time["h"] ,
                                     "m"=>$shift_time['m']])

                        @include("component.input._time",["id"=>"start_datetime","lable"=>" زمان شروع ",
                                    "h"=>$start_datetime["h"],"m"=>$start_datetime["m"]])

                        @include("component.input._time",["id"=>"end_datetime","lable"=>" زمان پایان ",
                                    "h"=>$end_datetime["h"],"m"=>$end_datetime["m"]])


                            @for ($i = 1; $i < 16; $i++)
                            @include("component.input._hidden",["id"=>"data[$i][worker_id]","value"=>$i])

                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"worker_id_".$i,
                                    "label"=>" نام شاغل ".$i,
                                    "option"=>$worker_option["items"],
                                    "val"=>$production_workers[$i-1]->worker_id??"",
                                    "text"=>(($production_workers[$i-1]->worker->firstname??"").(isset($production_workers[$i-1]->worker->lastname) ?" ":"").($production_workers[$i-1]->worker->lastname??"")),
                                    "class_col"=>""
                                    ])
                            </div>
                            <div class="col-md-6">
                                @include("component.input._text",["id"=>"data[".$i."][post_name]","lable"=>" پست سازمانی $i  ","value"=>$production_workers[$i-1]->post_name??"","class_col"=>"col-md-12"])
                            </div>
                            @endfor


                        </div>

                        @include("component.input._hidden",["id"=>"new_version","value"=>0])
                        <a href="{{route("production.datetime.create",[$production,$version-1])}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" id="submint_end" class="btn btn-primary">ثبت و ادامه  </button>
                        <button type="submit" id="submint_and_new" class="btn btn-primary">ذخیره  و ثبت ساعت جدید  </button>
                        <a href="{{route("production.datetime.delete",[$production,$version])}}" class="btn btn-danger" >حذف و بازگشت</a>

                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection



@section("styles")
@include("component.input.datepicker._script")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


@section("scripts")
<script>
$('#form1').validate({
            rules: {
                "production_date_value":"required",
                'data[1][post_name]': "required",
                'worker_id_1_auto': "required",
                "shift_time_h":"required",
                "start_datetime_h":"required",
                "end_datetime_h":"required",
            }
});
$("#submint_and_new").click(function(){
    $("#new_version").val("{{$version+1}}");
});
$("#submint_and").click(function(){
    $("#new_version").val("0");
});
</script>
@endsection
