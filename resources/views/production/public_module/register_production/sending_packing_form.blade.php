@extends('layouts.admin._master')
@section("page_header_title","داشبورد ".$machine_allocation->getTextOfThing("dashboard_caption")."-  ".
$machine_allocation->getTextOfThing("fullCaption")
)
@section('content')
    <form id="form1" autocomplete="off"
          action="{{route("production.public_module.register_production.submit_sending_packing_form",$machine_allocation)}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5> {{$machine_allocation->machine?"کارت تولید ":"دستور پیمان"}} {{$machine_allocation->production->serial()}}</h5>
            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-sm-12" style="overflow: auto">


                        @if($allow_get_details)
                            <table class="table table-styling center">
                                <tr>
                                    <td>ردیف</td>
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
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    @php $row=1; @endphp
                                    @foreach($list as $item)
                                        <td>
                                            {{$row++}}
                                        </td>
                                        <td>
                                            <input class="myCheckBox" type="checkbox"
                                                   name="packing_form_ids[{{$item->id}}]">
                                        </td>
                                        <td>{{$item->machine_allocation->product->code}}</td>
                                        <td>{{$item->machine_allocation->product->caption}}
                                            @if($machine_allocation->id !=$item->machine_allocation_id)
                                                <br/>
                                                <span style="font-size: 12px; " class="text-info">{{$item->machine_allocation->production->serial}}</span>
                                            @endif
                                        </td>
                                        <td>{{$item->packing_form->carrier->code??""}}</td>
                                        <td>{{$item->packing_form->getCode()}}</td>
                                        <td>{{$item->packing_form->packing_type->caption??""}}</td>
                                        <td>{{$item->packing_form->items()->count()}}</td>
                                        <td>{{$item->packing_form->getAllAmount("final_amount")}}</td>
                                        <td>{{$item->packing_form->getAllAmount("sub_amount")}}</td>

                                        <td>

                                            <a target="_blank"
                                               href="{{route("fabric_raw.packing_form.print_qr.index",$item->packing_form )}}">
                                                <i class="fa fa-print"></i>

                                            </a>


                                        </td>
                                </tr>
                                @endforeach
                            </table>
                        @else
                            <table class="table table-styling center">
                                <tr>
                                    <td>
                                        <input type="checkbox" id="select_all">
                                    </td>
                                    <th>فرم ورود</th>
                                    <th>کد کالا</th>
                                    <th>نام کالا</th>
                                    <th>درجه</th>
                                    <th>لات</th>
                                    <th>نوع بسته بندی</th>
                                    <th>تعداد بسته بندی</th>
                                    <th>{{$machine_allocation->product->unit->measurement}} کل</th>
                                    @if($machine_allocation->product->sub_unit)
                                        <th>{{$machine_allocation->product->sub_unit->measurement}} کل</th>
                                    @endif
                                    <th>مبلع</th>
                                    <th>ارزش افزوده</th>
                                    <th>مبلع کل</th>
                                </tr>
                                <tr>
                                    @foreach($list as $item)
                                        <td>
                                            <input class="myCheckBox" type="checkbox"
                                                   name="input_form_ids[{{$item->form_id}}]">
                                        </td>
                                        <td>{{$item->form->code}}</td>
                                        <td>{{$machine_allocation->product->code}}</td>
                                        <td>{{$machine_allocation->product->caption}}</td>
                                        <td>{{$item->degree->caption??""}}</td>
                                        <td>{{$item->lot_number->code??""}}</td>
                                        <td>{{$item->packing_type->fullCaption()}}</td>
                                        <td>{{$item->packing_form_number??""}}</td>
                                        <td>{{$item->amount??""}}</td>
                                        @if($machine_allocation->product->sub_unit)
                                            <td>{{$item->sub_amount??""}}</td>
                                        @endif
                                        <td>{{$item->price??""}}</td>
                                        <td>{{$item->tax_price??""}}</td>
                                        <td>{{$item->total_price_with_tax??""}}</td>
                                </tr>
                                @endforeach
                            </table>
                        @endif

                    </div>

                    @if($machine_allocation->InputFromLoadingRequired())
                        <div class="col-md-12">
                            <h5> اطلاعات بارگیری</h5>
                        </div>
                        @include("utility.transport.public._create_transport_view")
                    @endif

                    <div class="col-md-12">
                        <a href="{{route("production.public_module.register_production.index",$machine_allocation)}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('آیا از ارسال محصول به '+'{{$machine_allocation->machine?"انبار ":"کارفرما"}}'+' اطمینان دارید؟')">
                            ارسال به
                            {{$machine_allocation->machine?"انبار":"کارفرما"}}</button>

                    </div>

                </div>
            </div>
        </div>
    </form>
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
                "coordination_time_for_receive_product_value": "required",
                "time_h": "required",
            }
        });
        $("#select_all").change(function () {

            $(".myCheckBox").prop('checked', $("#select_all").is(':checked'));
        })
    </script>
@endsection
