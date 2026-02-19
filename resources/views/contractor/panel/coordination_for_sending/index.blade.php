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

                    @if($contractor->show_packing_forms_in_warehouse)
                        <table class="table table-styling center">
                            <tr>
                                <th colspan="5">لیست بسته بندی های موجود در انبار کارفرما</th>
                            </tr>
                            <tr>
                                <th>ردیف</th>
                                <th>کد کالا</th>
                                <th>نام کالا</th>
                                <th>کد بسته بندی</th>
                                <th> تعداد اقلام بسته بندی</th>
                                <th>مقدار اصلی</th>
                                <th>مقدار فرعی</th>
                                <th> کارت های تولید</th>
                                <th></th>
                            </tr>


                            @foreach($package_list as $item)
                                @php $row=$item ["package"]->firstItem();;@endphp
                                @foreach($item ["package"] as $package)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>{{$item ["bom_item"]->material->code}}</td>
                                        <td>{{$item ["bom_item"]->material->caption}}</td>
                                        <td>{{$package->code}}</td>
                                        <td>{{$package->items->count()}}</td>
                                        <td>{{$package->getAllAmount("final_amount")}} {{$package->items()->first()->product->unit->caption??"**"}}</td>
                                        <td>@if($package->items()->first()->product->sub_unit)
                                                {{$package->getAllAmount("sub_amount")}} {{$package->items()->first()->product->sub_unit->caption??"**"}}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#!" data-id="{{$package->id}}" class="md-trigger md-setperspective"
                                               data-modal="modal-11" href="#!"><i
                                                        class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="7">
                                        <div class="text-center">
                                            {{$item ["package"]->links('pagination::bootstrap-4')}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    @endif

                    @if($contractor_allocation->allocation->items()->count()>1)
                        <table class="table table-styling center">
                            <tr>
                                <th colspan="5">لیست کارت های پیمان همراه</th>
                            </tr>
                            <tr>
                                <th>ردیف</th>
                                <th>شماره کارت پیمان</th>
                                <th>کد کالا</th>
                                <th>نام کالا</th>
                                <th>مقدار ({{$contractor_allocation->product->unit->caption}})</th>
                            </tr>


                            @foreach($contractor_allocation->allocation->items()->where("id","!=",$contractor_allocation->id)->get() as $allcation_item)
                                @php $row=0;@endphp

                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>{{$allcation_item->production->serial()}}</td>
                                    <td>{{$allcation_item->product->code}}</td>
                                    <td>{{$allcation_item->product->caption}}</td>
                                    <td>{{$allcation_item->allocation_amount}}</td>

                                </tr>
                            @endforeach


                        </table>
                    @endif

                    <form id="form1" autocomplete="off"
                          action="{{route("contractor.panel.coordination_for_sending.submit",$contractor_allocation)}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="alert alert-info">
                            لطفا تاریخ و ساعت ارسال مواد اولیه را وارد نمایید
                        </div>


                        @include("component.input.datepicker.jalali_datepicker._jalali_datepicker",["id"=>"coordination_time_for_receive_product","hasTime"=>1,"min_date"=>$min_date,"lable"=>"  تاریخ و ساعت ","class_col"=>"col-md-3","value"=>null])


                        @include("component.input._textarea",["label"=>"توضیحات پیمانکار","id"=>"message","height"=>"200px"])

                        <a href="{{route("contractor.panel.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">ثبت</button>

                    </form>
                </div>
            </div>
        </div>


    </div>

@endsection

@section("modals")

    @include("component.modal.md-modal._modal",[
        "id"=>"11",
        "url"=>"",
        "theme"=>"primary",
        "title"=>"لیست کارت های تولید",
        "content"=>view("contractor.panel.coordination_for_sending._production_card_list",[])->render()]
        )

@endsection

@section("styles")

    @include("component.input.datepicker.jalali_datepicker._style")
    @include("component.modal.md-modal._style")
    <style>
        .md-modal {
            max-width: 80%;
        }
    </style>
@endsection
@section("scripts")
    @include("component.input.datepicker.jalali_datepicker._script")
    @include("component.modal.md-modal._script")
    <script>


        $('#form1').validate({
            rules: {
                "coordination_time_for_receive_product_value": "required",
                "time_h": "required",
            },

        });


        $(".md-trigger").click(function () {

            request = $.ajax({
                url: "{{url("api/contractor/coordination_for_sending/get_production_info")}}",
                type: "post",
                data: {
                    "packing_form_id": $(this).data("id"),
                    "contractor_allocation_id": '{{$contractor_allocation->id}}',
                    "contractor_id": '{{$contractor_allocation->contractor_id}}',
                }
            });
            request.done(function (response, textStatus, jqXHR) {

                $("#production_info").html(response);
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // Log the error to the console
                console.error(
                    "The following error occurred: " +
                    textStatus, errorThrown
                );
            });

        })


    </script>
@endsection
