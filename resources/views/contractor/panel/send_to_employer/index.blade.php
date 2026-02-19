@extends('layouts.admin._master')
@section("page_header_title","داشبورد پیمانکاران -  ".$contractor->fullCaption())

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> دستور پیمان {{$contractor_allocation->production->serial()}}</h5>
                </div>
                <div class="card-block">

                    <form id="form1" autocomplete="off"
                          action="{{route("contractor.panel.send_to_employer.submit",$contractor_allocation)}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf

                                <table class="table table-styling center">
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="select_all">
                                        </td>
                                        <th>کد کالا</th>
                                        <th>نام کالا</th>
                                        <th>شماره حامل</th>
                                        <th>کد بسته بندی</th>
                                        <th>نوع بسته بندی</th>
                                        <th>تعداد بسته بندی فرعی/اقلام</th>
                                        <th>مقدار</th>
                                        <th>مقدار فرعی</th>
                                        <th>وضعیت</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        @foreach($contractor_packing_list as $item)
                                            <td>
                                               <input class="myCheckBox" type="checkbox" name="data[{{$item->id}}]">
                                            </td>
                                            <td>{{$contractor_allocation->product->code}}</td>
                                            <td>{{$contractor_allocation->product->caption}}</td>
                                            <td>{{$item->packing_form->carrier->code??""}}</td>
                                            <td>{{$item->packing_form->getCode()}}</td>
                                            <td>{{$item->packing_form->packing_type->caption??""}}</td>
                                            <td>{{$item->packing_form->items()->count()}}</td>
                                            <td>{{$item->packing_form->getAllAmount("final_amount")}}</td>
                                            <td>{{$item->packing_form->getAllAmount("sub_amount")}}</td>

                                            <td>

                                                <a href="{{route("fabric_raw.packing_form.print_qr.index",$item->packing_form )}}">
                                                    <i class="fa fa-print"></i>

                                                </a>


                                            </td>
                                    </tr>
                                    @endforeach
                                </table>


                        <a href="{{route("contractor.panel.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary" onclick="return confirm('آیا از ارسال محصول به کارفرما اطمینان دارید؟')">ارسال به کارفرما</button>

                    </form>
                </div>
            </div>
        </div>


    </div>

@endsection
@section("styles")

    @include("component.input.datepicker._script")

@endsection
@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "coordination_time_for_receive_product_value": "required",
                "time_h": "required",
            }
        });
        $("#select_all").change(function () {

            $(".myCheckBox").prop('checked', $("#select_all").is(':checked'));
        })
    </script>
@endsection
