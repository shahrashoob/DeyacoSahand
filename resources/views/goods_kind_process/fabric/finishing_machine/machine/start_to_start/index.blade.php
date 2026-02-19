@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    @php $unit_caption=$machine_allocation->product->unit->caption;@endphp
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>   شروع عملیات STS
                    </h5>
                </div>
            </div>
                    <form id="form1"
                          action="{{route("fabric.finishing_machine.machine.start_to_start.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="w-100"></div>

                        <div class="col-xl-3 col-lg-12">
                            <div class="card task-board-left">
                                <div class="card-header">
                                    <h5>ماشین (های) {{$start_to_start_line_product_station->machine_type->caption}}</h5>
                                </div>
                                <div class="card-block">



                                    <form id="form1" action="{{route("fabric.finishing_machine.machine.start_to_start.submit",
                                                        [$machine])}}"
                                          method="post"
                                          autocomplete="off"
                                          novalidate="novalidate">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                @include("component.input._aotocomplet2",[
                                                    "id"=>"next_machine_id",
                                                    "label"=>" تخصیص ماشین   ",
                                                    "option"=>$start_to_start_line_product_station->machine_type->
                                                    getPossibilityOfAllocationMachine(true,0, $machine_allocation->line_product_station)["items"],
                                                    "val"=>"",
                                                    "text"=>"",
                                                    "class_col"=>""
                                                    ])
                                            </div>

                                        </div>
                                        <button type="submit" class="btn btn-success">ثبت و تایید</button>
                                    </form>

                                </div>
                            </div>
                        </div>

                        <br/>

                        <div class="col-md-12">
                            <a href="{{route("fabric.finishing_machine.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                        </div>


                    </form>
                </div>


        </div>



@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "var": "required",
            }
        });
    </script>
@endsection
