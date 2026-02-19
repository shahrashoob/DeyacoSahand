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


                    @if(count($packing_form->items)>0)
                        <div class="card">
                            <div
                                class="card-header">
                                <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route1"
                                                    aria-expanded="false" aria-controls="collapseOne" class="collapsed">

                                        لیست اقلام بسته بندی
                                        {{$packing_form->getCode()}}


                                    </a>
                                </h5>

                            </div>
                            <div class=" multi-collapse collapse show " id="route1"
                                 style=""
                                 data-parent="#accordionExample">


                                <div class="col-sm-12">
                                    <br/>
                                    <table class="table table-styling center">
                                        <tr>
                                            <th>کد کالا</th>
                                            <th>نام کالا</th>
                                            <th>نوع بسته بندی</th>
                                            <th>شماره حامل</th>
                                            <th>درجه</th>
                                            <th>همبافت (لات)</th>
                                            <th>مقدار</th>
                                            <th>مقدار فرعی</th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            @foreach($packing_form->items as $item)
                                                <td>{{$item->product->code}}</td>
                                                <td>{{$item->product->caption}}</td>
                                                <td>{{$packing_form->packing_type->caption??""}}</td>
                                                <td>{{$item->packing_form->carrier->code??""}}</td>
                                                <td>{{$item->degree->code??''}}</td>
                                                <td>{{$item->lot_number->code??''}}</td>
                                                <td>{{$item->final_amount}}</td>
                                                <td>{{$item->sub_amount}}</td>

                                        </tr>
                                        @endforeach
                                    </table>
                                </div>

                                <br/>
                                <div class="center">
                                    @if($packing_form->parent_packing_form_sub_packing())
                                    <a class="btn btn-outline-dark"
                                       href="{{route("contractor.panel.dashboard.view_packing",[$contractor_allocation,$packing_form->parent_packing_form_sub_packing()->parent_packing_form_id])}}">

                                        بازگشت

                                    </a>
                                    @else
                                        <a class="btn btn-outline-dark"
                                           href="{{route("contractor.panel.dashboard.view",[$contractor_allocation])}}">

                                            بازگشت

                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

{{--                    @if(count($packing_form->sub_packing)>0)--}}
{{--                        <div class="card">--}}
{{--                            <div--}}
{{--                                class="card-header">--}}
{{--                                <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route1"--}}
{{--                                                    aria-expanded="false" aria-controls="collapseOne" class="collapsed">--}}

{{--                                        لیست بسته بندی های در انتظار ثبت--}}


{{--                                    </a>--}}
{{--                                </h5>--}}
{{--                                <div class="card-header-right" style="margin-top: 10px">--}}
{{--                                    <a class="text-primary text-success"--}}
{{--                                       href="{{route("contractor.panel.register_production.add_new_packing",[$contractor_allocation, 1,$packing_form])}}">--}}
{{--                                        <i class="fa fa-plus"></i> افزودن بسته بندی جدید--}}
{{--                                    </a>--}}

{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class=" multi-collapse collapse show " id="route1"--}}
{{--                                 style=""--}}
{{--                                 data-parent="#accordionExample">--}}


{{--                                <div class="col-sm-12">--}}

{{--                                    <table class="table table-styling center">--}}
{{--                                        <tr>--}}
{{--                                            <th>کد کالا</th>--}}
{{--                                            <th>نام کالا</th>--}}
{{--                                            <th>شماره حامل</th>--}}
{{--                                            <th>کد بسته بندی</th>--}}
{{--                                            <th>نوع بسته بندی</th>--}}
{{--                                            <th>تعداد بسته بندی فرعی/اقلام</th>--}}
{{--                                            <th>مقدار</th>--}}
{{--                                            <th>مقدار فرعی</th>--}}
{{--                                            <th></th>--}}
{{--                                        </tr>--}}
{{--                                        <tr>--}}
{{--                                            @foreach($packing_form->sub_packing as $item)--}}
{{--                                                <td>{{$contractor_allocation->product->code}}</td>--}}
{{--                                                <td>{{$contractor_allocation->product->caption}}</td>--}}
{{--                                                <td>{{$item->packing_form->carrier->code??""}}</td>--}}
{{--                                                <td>--}}
{{--                                                    <a href="{{route("contractor.panel.dashboard.view_packing",[$contractor_allocation,$item->packing_form ])}}">--}}
{{--                                                        {{$item->packing_form->getCode()}}--}}

{{--                                                    </a>--}}

{{--                                                </td>--}}
{{--                                                <td>{{$item->packing_form->packing_type->caption??""}}</td>--}}
{{--                                                <td>{{$item->packing_form->sub_packing()->count()+$item->packing_form->items()->count()}}</td>--}}
{{--                                                <td>{{$item->packing_form->items()->sum("final_amount")??""}}</td>--}}
{{--                                                <td>{{$item->packing_form->items()->sum("sub_amount")??""}}</td>--}}

{{--                                                <td>--}}

{{--                                                </td>--}}
{{--                                        </tr>--}}
{{--                                        @endforeach--}}
{{--                                    </table>--}}
{{--                                </div>--}}

{{--                                <br/>--}}
{{--                                <div class="center">--}}
{{--                                    <a class="btn btn-outline-dark"--}}
{{--                                       href="{{route("contractor.panel.dashboard.view",[$contractor_allocation])}}">--}}

{{--                                        بازگشت--}}

{{--                                    </a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}
                </div>
            </div>
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
                "packing_type_id_auto": "required",
                "degree_id_auto": "required",
                "amount": {
                    required: true,
                    min: 1
                },
                "carrier_code": "required"
            }
        });
    </script>
@endsection
