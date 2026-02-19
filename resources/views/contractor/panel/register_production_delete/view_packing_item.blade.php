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


                    <div class="card">
                        <div
                            class="card-header">
                            <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route1"
                                                aria-expanded="false" aria-controls="collapseOne" class="collapsed">

                                    لیست اقلام بسته بندی
                                    {{$packing_form->getCode()}}


                                </a>
                            </h5>
                            <div class="card-header-right" style="margin-top: 10px">
                                <a class="text-success"
                                   href="{{route("contractor.panel.register_production.add_new_product_to_packing",[$contractor_allocation, $packing_form])}}">
                                    <i class="fa fa-plus"></i> افزودن مقدار جدید
                                </a>

                            </div>
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
                                        <th>
                                            {{$packing_form->getUnitCaption("unit","measurement")}}
                                        </th>
                                        <th>
                                            {{$packing_form->getUnitCaption("sub_unit","measurement")}}
                                        </th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        @foreach($packing_form->items as $item)
                                            <td>{{$item->product->code}}</td>
                                            <td>{{$item->product->caption}}</td>
                                            <td>{{$packing_form->packing_type->caption??""}}</td>
                                            <td>{{$item->packing_form->carrier->code??""}}</td>
                                            <td>{{$item->degree->code??''}} - {{$item->degree->caption??''}}</td>
                                            <td>{{$item->lot_number->code??''}}</td>
                                            <td>{{$item->final_amount}}</td>
                                            <td>{{$item->sub_amount}}</td>
                                            <td>
                                                <a class="text-danger"
                                                   onclick="return confirm('آیا از حذف اطمینان دارید؟')"
                                                   href="{{route("contractor.panel.register_production.delete_packing_item",[$contractor_allocation,$item->packing_form,$item])}}">
                                                    <i class="fa fa-trash"></i>

                                                </a>
                                            </td>
                                    </tr>
                                    @endforeach

                                </table>
                            </div>

                            <br/>
                            <div class="center">
                                @if($packing_form->parent_packing_form_sub_packing())
                                    <a class="btn btn-outline-dark"
                                       href="{{route("contractor.panel.register_production.view_packing",[$contractor_allocation,$packing_form->parent_packing_form_sub_packing()->parent_packing_form_id])}}">

                                        بازگشت

                                    </a>
                                @else
                                    <a class="btn btn-outline-dark"
                                       href="{{route("contractor.panel.register_production.index",[$contractor_allocation])}}">

                                        بازگشت

                                    </a>
                                @endif

                            </div>
                        </div>
                    </div>


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
