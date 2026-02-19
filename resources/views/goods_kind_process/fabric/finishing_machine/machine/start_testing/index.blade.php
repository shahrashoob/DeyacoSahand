@extends('layouts.admin._master')
@section("page_header_title"," داشبورد ماشین آلات ")
@section("content")
    @php $unit_caption=$machine_allocation->product->unit->caption;@endphp
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>  {{$machine->fullCaption()}} - شروع تست کالا
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("fabric.finishing_machine.machine.start_testing.submit",$machine)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        @include("component.input._lable",["lable"=>"مقدار تست مورد نیاز کالا","value"=>$testing_amount." ".$unit_caption])
                        <div class="w-100"></div>

                        <div class="table-responsive center" style="font-size: 12px">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th></th>
                                    <th>کد بسته بندی</th>
                                    <th>ماده اولیه</th>


                                    <th>مقدار مورد نیاز تست</th>

                                </tr>

                                </thead>
                                <tbody>

                                @php $row=1; @endphp
                                @foreach($current_machine_inputs as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td><input type="checkbox" name="current_machine_input_checkbox[{{$item->id}}]">
                                        </td>
                                        <td>
                                            <select  class="option" name="current_machine_input_select[{{$item->id}}]" style="width: 70px">

                                                @foreach($packing_form_list as $packing_form_item)

                                                    @if($packing_form_item->product_id == $item->material_id)
                                                        <option  value="{{$packing_form_item->id}}" >
                                                            {{$packing_form_item->code}}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
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
                            <button type="submit" class="btn btn-success">تایید مقدار تست و شروع</button>
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
    <style>
        .option{
            width: 150px !important;
            text-align: center;
            padding: 2px 5px
        }
    </style>
@endsection


@section("scripts")

    <script>

        $('#form1').validate({
            rules: {
                firstname: "required",
                @foreach($current_machine_inputs as $item)
                        'current_machine_input_select[{{$item->id}}]': "required",
                @endforeach
            }
        });

    </script>
@endsection


