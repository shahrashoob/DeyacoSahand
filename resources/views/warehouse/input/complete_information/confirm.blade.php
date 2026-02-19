@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار  ")

@section('content')


    <form id="form1" autocomplete="off" action="{{route("wh.input.complete_information.submit_confirm",$form)}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> فرم ورود به انبار {{$form->code}}</h5>
                    </div>
                    <div class="card-block  ">
                        @include("warehouse.dashboard._input_form_info")


                    </div>
                </div>
            </div>

            @include("warehouse.dashboard._general_item_list")

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>مشخصات بسته بندی ها</h5>
                    </div>

                    @include("component.input._hidden",["id"=>"packing_form_rows","value"=>($packing_form_rows)])
                    @include("component.input._hidden",["id"=>"reservoirs_rows","value"=>($reservoirs_rows)])
                    @php $warehouse_storage_type_id=$form->general_items()->first()->warehouse_storage_type_id;@endphp
                  @if($warehouse_storage_type_id==2)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive center">
                                    <table class="table table-styling">
                                        <thead>
                                        <tr>
                                            <th style="width: 10px">ردیف</th>

                                            <th>کد کالا</th>
                                            <th>درجه</th>
                                            <th>لات</th>

                                            <th> وزن ناخالص</th>

                                            <th>وزن خالص</th>

                                            <th>مقدار اصلی</th>

                                            <th>تعداد بسته بندی های فرعی</th>
                                            <th>نوع بسته بندی</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach(json_decode($packing_form_rows) as $item)
                                            <tr>
                                                <td>{{++$row}}</td>

                                                <td>{{$form_general_items[$item->form_general_item_id]->product->code}}</td>
                                                <td>{{$form_general_items[$item->form_general_item_id]->degree->caption}}</td>
                                                <td>{{$form_general_items[$item->form_general_item_id]->lot_number->code}}</td>
                                                <td>{{$item->gross_weight??""}}</td>


                                                <td>{{$item->weight??""}}</td>

                                                <td>{{$item->final_amount??""}}</td>


                                                <td>{{$item->sub_packing_form_number??""}}</td>
                                                <td>{{$form_general_items[$item->form_general_item_id]->packing_type->fullCaption()}}</td>

                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>
                    @elseif($warehouse_storage_type_id==3)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive center">
                                    <table class="table table-styling">
                                        <thead>
                                        <tr>
                                            <th style="width: 10px">ردیف</th>

                                            <th>کد کالا</th>
                                            <th>درجه</th>
                                            <th>لات</th>



                                            <th>نوع بسته بندی</th>
                                            <th>مقدار اصلی</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach(json_decode($reservoirs_rows) as $item)
                                            <tr>
                                                <td>{{++$row}}</td>

                                                <td>{{$form_general_items[$item->form_general_item_id]->product->code}}</td>
                                                <td>{{$form_general_items[$item->form_general_item_id]->degree->caption}}</td>
                                                <td>{{$form_general_items[$item->form_general_item_id]->lot_number->code}}</td>
                                                <td>{{$form_general_items[$item->form_general_item_id]->warehouse_storage_type->caption}}</td>
                                                <td>{{$item->unit_amount??""}}</td>

                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>
                    @else
                      @php 1/0;@endphp
                  @endif


                </div>
            </div>

            <div class="col-md-12 center">


                <a class="btn btn-outline-dark" href="{{route("wh.input.complete_information.index",$form)}}">بازگشت</a>
                <a class="btn btn-primary" href="{{route("wh.input.complete_information.index",$form)}}">ویرایش</a>
                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown"
                        aria-haspopup="true" style="width: 140px"
                        aria-expanded="false">تایید نهایی
                </button>
                <div class="dropdown-menu" style="text-align: center">
                    <a class="dropdown-item" id="btn_confirm_print"> تایید نهایی و چاپ فرم های بسته بندی</a>

                    <a class="dropdown-item" id="btn_confirm_back">تایید نهایی </a>


                </div>
                @include("component.input._hidden",["id"=>"print","value"=>0])
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
        $("#btn_confirm_print").click(function () {
            $("#print").val("print_1");
            $("#form1").submit();
        })
        $("#btn_confirm_back").click(function () {
            $("#print").val("back");
            $("#form1").submit();
        })
        $('#form1').validate({
            rules: {
                "trans_kind_id_auto": "required",
            }
        });
    </script>

@endsection

