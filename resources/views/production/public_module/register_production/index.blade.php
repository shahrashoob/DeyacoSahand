    @extends('layouts.admin._master')
    @section("page_header_title","داشبورد ".$machine_allocation->getTextOfThing("dashboard_caption")."-  ".
    $machine_allocation->getTextOfThing("fullCaption")
    )

    @section('content')
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> {{$machine_allocation->getTextOfThing("production_caption")}} {{$machine_allocation->production->serial()}}</h5>
                    </div>
                    <div class="card-block">

                        @include("production.public_module.register_production._source_production_form_item")
                        <div class="accordion" id="accordionExample">


                            <div class="card">
                                <div
                                        class="card-header">
                                    <h5 class="mb-0">
                                        <a href="#!" data-toggle="collapse" data-target="#route1"
                                           aria-expanded="false" aria-controls="collapseOne" class="collapsed">

                                            لیست بسته بندی های در انتظار ثبت

                                        </a>
                                    </h5>

                                    <a class="text-primary"
                                       href="{{route("production.public_module.register_production.sending_packing_form",[$machine_allocation, 0])}}">
                                        <i class="fa fa-arrow-alt-circle-up"></i> ارسال به
                                        {{$machine_allocation->getTextOfThing("warehouse_caption")}}
                                    </a>
                                    @if(!in_array($machine_allocation->production->waiting_status_id, [5310106,5310020]))

                                        {{--                                    اگر ثبت تولید برای ماشین است، یا برای کارفرمایی است که لازم است جزئیات اطلاعات را وارد نماید--}}
                                        @if($machine_allocation->machine || ($machine_allocation->contractor && $machine_allocation->contractor->get_packing_form_details) )
                                            <a class="text-success"
                                               href="{{route("production.public_module.register_production.add_new_packing",[$machine_allocation, 0,$source_production_form_item->id??0])}}">
                                                <i class="fa fa-plus"></i> افزودن بسته بندی جدید
                                            </a>

                                            <a class="text-success"
                                               href="{{route("production.public_module.upload.index",[$machine_allocation])}}">
                                                <i class="fa fa-file"></i>
                                                بارگذاری فایل تولید
                                            </a>
                                        @else
                                            <a class="text-success"
                                               href="{{route("production.public_module.register_production.add_without_details",[$machine_allocation])}}">
                                                <i class="fa fa-plus"></i>
                                                {{--                                            // ثبت تولید--}}
                                                {{$machine_allocation->getTextOfThing("btn_register")}}
                                            </a>


                                        @endif
                                    @endif


                                </div>
                                @if(count($machine_packing_list) > 0)
                                    <div class="row">
                                        <div class="col-sm-12" style="overflow: auto">

                                            <table class="table table-styling center">
                                                <tr>
                                                    <th>ردیف</th>
                                                    <th>کد کالا</th>
                                                    <th>نام کالا</th>
                                                    <th>شماره حامل</th>
                                                    <th>کد بسته بندی</th>
                                                    <th>نوع بسته بندی</th>
                                                    <th>تعداد بسته بندی<br/> اقلام</th>
                                                    <th>مقدار</th>
                                                    <th>مقدار فرعی</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    @php $i=1; @endphp
                                                    @foreach($machine_packing_list as $item)
                                                        <td>{{$i++}}</td>
                                                        <td>{{$item->machine_allocation->product->code}}</td>
                                                        <td>{{$item->machine_allocation->product->caption}}

                                                            @if($machine_allocation->id !=$item->machine_allocation_id)
                                                                <br/>
                                                                <span style="font-size: 12px; " class="text-info">{{$item->machine_allocation->production->serial}}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{$item->packing_form->carrier->code??""}}</td>
                                                        <td>
                                                            @if(!$item->need_to_complete_information)
                                                                {{$item->packing_form->getCode()}}

                                                            @else

                                                                <a
                                                                        href="{{route("production.public_module.register_production.complete_information",[$item->machine_allocation,$item,$source_production_form_item->id??0])}}">
                                                                    {{$item->packing_form->getCode()}}
                                                                </a>
                                                            @endif


                                                        </td>
                                                        <td>{{$item->packing_form->packing_type->caption??""}}</td>
                                                        <td>{{$item->packing_form->getItemCount()}}</td>
                                                        <td>{{$item->packing_form->getAllAmount("final_amount")}}</td>
                                                        <td>{{$item->packing_form->getAllAmount("sub_amount")}}</td>
                                                        <td>

                                                            @if($item->packing_form->status_id == 7007011)

                                                                <a
                                                                        href="{{route("production.public_module.register_production.copy_packing_form",[$item->machine_allocation,$item->packing_form])}}">
                                                                    <i class="fa fas fa-copy text-primary"></i>
                                                                </a>
                                                                &nbsp;
                                                            @endif

                                                            <a
                                                                    href="{{route("production.public_module.register_production.print_packing_form",[$item->machine_allocation,$item->packing_form])}}">
                                                                <i class="fa fa-print text-info"></i>
                                                            </a>
                                                            <a
                                                                    href="{{route("production.public_module.register_production.download_packing_form",[$item->machine_allocation,$item->packing_form])}}">
                                                                <i class="fa fa-download text-info"></i>
                                                            </a>


                                                            {{--                                                        @if($item->packing_form->packing_type->layers()->count() >1)--}}
                                                            {{--                                                        <a href="{{route("contractor.panel.register_production.view_packing",[$machine_allocation,$item->packing_form ])}}">--}}
                                                            {{--                                                            <i class="fa fa-plus"></i>--}}
                                                            {{--                                                            ثبت بسته بندی فرعی--}}

                                                            {{--                                                        </a>--}}
                                                            {{--                                                        @else--}}
                                                            {{--                                                        <a href="{{route("fabric.special_production.machine.register_production.view_packing",[$machine_allocation,$item->packing_form ])}}">--}}
                                                            {{--                                                            <i class="fa fa-eye"></i>--}}


                                                            {{--                                                        </a>--}}
                                                            {{--                                                        @endif--}}

                                                            <a class="text-danger"
                                                               onclick="return confirm('آیا از حذف اطمینان دارید؟')"
                                                               href="{{route("production.public_module.register_production.delete_packing_form",[$item->machine_allocation,$item->packing_form])}}">
                                                                <i class="fa fa-trash"></i>
                                                            </a>

                                                        </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>

                                        <br/>
                                    </div>
                                @endif

                                @if(count($form_general_item) > 0)
                                    <div class="row">
                                        <div class="col-sm-12" style="overflow: auto">

                                            <table class="table table-styling center">
                                                <tr>
                                                    <th>کارت
                                                    {{$machine_allocation->contracto?"پیمان":"تولید"}}
                                                    </th>
                                                    <th>کد کالا</th>
                                                    <th>نام کالا</th>
                                                    <th>درجه</th>
                                                    <th>لات</th>
                                                    <th>نوع بسته بندی</th>
                                                    <th>تعداد بسته بندی</th>
                                                    <th>{{$machine_allocation->product->unit->measurement}} کل</th>
                                                    @if($machine_allocation->product->sub_unit)
                                                        <th>{{$machine_allocation->product->sub_unit->measurement}}کل
                                                        </th>
                                                    @endif
                                                    <th>مبلغ بدون ارزش افزوده (ریال)</th>
                                                    <th>ارزش افزوده (ریال)</th>
                                                    <th> مبلع کل (ریال)</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    @foreach($form_general_item as $item)
                                                        <td>{{$item->machine_allocation->production->serial}}</td>
                                                        <td>{{$item->machine_allocation->product->code}}</td>
                                                        <td>{{$item->machine_allocation->product->caption}}</td>
                                                        <td>{{$item->degree->caption??""}}</td>
                                                        <td>{{$item->lot_number->code??""}}</td>
                                                        <td>{{$item->packing_type->fullCaption()}}</td>
                                                        <td>{{$item->packing_form_number??""}}</td>
                                                        <td>{{$item->amount??""}}</td>
                                                        @if($machine_allocation->product->sub_unit)
                                                            <td>{{$item->sub_amount??""}}</td>
                                                        @endif
                                                        <td>{{number_format($item->price??0)}}</td>
                                                        <td>{{number_format($item->tax_price??0)}}</td>
                                                        <td>{{number_format($item->total_price_with_tax??0)}}</td>
                                                        <td>
                                                            <a class="text-danger"
                                                               onclick="return confirm('آیا از حذف اطمینان دارید؟')"
                                                               href="{{route("production.public_module.register_production.delete_form_general_item",[$machine_allocation,$item])}}">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>

                                        <br/>
                                    </div>
                                @endif


                            </div>
                            <div class="col-md-12 center">
                                <form id="form1" autocomplete="off"
                                      action="{{route("production.public_module.register_production.submit_register_production",[$machine_allocation,$source_production_form_item->id??0])}}"
                                      method="post"
                                      novalidate="novalidate">
                                    @csrf

                                    @if($machine_allocation->machine)
                                        <a class="btn btn-outline-dark" style="width: 140px"
                                           href="{{route("production.machine.view",$machine_allocation->machine)}}">

                                            بازگشت

                                        </a>
                                    @elseif($machine_allocation->contractor)
                                        <a class="btn btn-outline-dark" style="width: 140px"
                                           href="{{route("contractor.panel.dashboard.view",$machine_allocation)}}">

                                            بازگشت

                                        </a>
                                    @elseif($machine_allocation->order)
                                        <a class="btn btn-outline-dark" style="width: 140px"
                                           href="{{route("sales.dashboard.view_order",$machine_allocation->order)}}">

                                            بازگشت

                                        </a>
                                    @endif
                                    @if($can_production_terminate && !$source_production_form_item)
                                        <a class="btn btn-primary"
                                           href="{{route($route_path."terminate_production",[$machine_allocation,"terminate"])}}">

                                            خاتمه یافته کردن
                                            {{$machine_allocation->machine?"کارت تولید ":"دستور پیمان"}}

                                        </a>
                                    @endif

                                    @if(count($machine_packing_list)>0 || count($form_general_item) > 0)

                                        <button type="submit" class="btn btn-primary"
                                                onclick="return confirm('آیا از ثبت نهایی  اطمینان دارید؟')"
                                                style="width: 160px">
                                            {{--                                        ثبت نهایی تولید--}}
                                            {{$machine_allocation->getTextOfThing("final_button_text")}}
                                        </button>


                                        @if(!$machine_allocation->InputFromLoadingRequired())
                                            {{--                                        اگر بارگیری نیاز ندارد، می تواند دو کار را با هم انجام دهد، در غیر این صورت باید یکی یکی انجام شود.--}}
                                            <a class="btn btn-primary "
                                               onclick="return confirm('آیا از ثبت نهایی و ارسال اطمینان دارید.')"
                                               href="{{route("production.public_module.register_production.register_production_and_send_to_warehouse",[$machine_allocation,$source_production_form_item->id??0])}}">

                                                {{$machine_allocation->getTextOfThing("final_button_and_send_text")}}

                                            </a>
                                        @endif
                                    @endif

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 center">

                <img class="example-image" style="max-height: 300px;"
                     src="{{asset("upload/product/".($machine_allocation->product->image->filename??''))}}" alt=""
                     style="width: 90%; height: 90%"/>
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
