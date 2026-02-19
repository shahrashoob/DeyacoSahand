@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    @php $unit_caption=$machine_allocation->product->unit->caption;@endphp
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine->fullCaption()}} - بررسی تست کالا
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric.finishing_machine.machine.confirm_testing.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        @include("component.input._lable",["lable"=>"مقدار تست انجام شده برای کالا","value"=>$testing_amount." ".$unit_caption])
                        <div class="w-100">BOM مصرف شده برای تولید مقدار تست</div>

                        <div class="table-responsive center" style="font-size: 12px">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ماده اولیه</th>



                                    <th>مقدار مورد نیاز تست</th>
                                </tr>

                                </thead>
                                <tbody>

                                @php $row=1; @endphp
                                @foreach($current_machine_inputs as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>

                                            {{($item->material->code??"")." - ".($item->material->caption??"")}}

                                        </td>

                                      

                                        <td>{{round($item->amount_for_one_unit() * $testing_amount,7)}} {{$unit_caption}}</td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>


                        <br/>

                        <div class="col-md-12">
                            <a href="{{route("fabric.finishing_machine.machine.dashboard.view",$machine)}}"
                               class="btn btn-outline-dark">بازگشت</a>
                            <button type="submit" class="btn btn-success">تست انجام شده مورد تایید است</button>

                            <a href="{{route("fabric.finishing_machine.machine.confirm_testing.change_bom",$machine)}}"
                               class="btn btn-danger">عدم تایید تست و ویرایش BOM</a>
                        </div>


                    </form>
                </div>
            </div>

        </div>
        <div class="col-md-12 center">


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
