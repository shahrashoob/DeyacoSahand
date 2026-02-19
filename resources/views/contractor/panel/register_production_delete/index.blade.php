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


                    <div class="accordion" id="accordionExample">


                        <div class="card">
                            <div
                                class="card-header">
                                <h5 class="mb-0"><a href="#!" data-toggle="collapse" data-target="#route1"
                                                    aria-expanded="false" aria-controls="collapseOne" class="collapsed">

                                        لیست بسته بندی های در انتظار ثبت

                                    </a>
                                </h5>
                                <div class="card-header-right" style="margin-top: 10px">


                                    <a class="text-primary"
                                       href="{{route("contractor.panel.upload_packing_form.index",$contractor_allocation)}}">
                                        <i class="fa fa-upload"></i> بارگذاری فایل
                                    </a>
                                    <a class="text-success"
                                       href="{{route("contractor.panel.register_production.add_new_packing",[$contractor_allocation, 0,0])}}">
                                        <i class="fa fa-plus"></i> افزودن بسته بندی جدید
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12" style="overflow: auto">

                                    <table class="table table-styling center">
                                        <tr>
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
                                            @foreach($contractor_packing_list as $item)
                                                <td>{{$contractor_allocation->product->code}}</td>
                                                <td>{{$contractor_allocation->product->caption}}</td>
                                                <td>{{$item->packing_form->carrier->code??""}}</td>
                                                <td>{{$item->packing_form->getCode()}}</td>
                                                <td>{{$item->packing_form->packing_type->caption??""}}</td>
                                                <td>{{$item->packing_form->items()->count()}}</td>
                                                <td>{{$item->packing_form->getAllAmount("final_amount")}}</td>
                                                <td>{{$item->packing_form->getAllAmount("sub_amount")}}</td>
                                                <td>
                                                    <a href="{{route("fabric_raw.packing_form.print_qr.download",$item->packing_form)}}">
                                                        <i class="fa fa-download text-info"></i>
                                                    </a>


                                                    @if($item->packing_form->packing_type->layers()->count() >1)
                                                        <a href="{{route("contractor.panel.register_production.view_packing",[$contractor_allocation,$item->packing_form ])}}">
                                                            <i class="fa fa-plus"></i>
                                                            ثبت بسته بندی فرعی

                                                        </a>
                                                    @else
                                                        <a href="{{route("contractor.panel.register_production.view_packing",[$contractor_allocation,$item->packing_form ])}}">
                                                            <i class="fa fa-eye"></i>


                                                        </a>
                                                    @endif

                                                    <a class="text-danger"
                                                       onclick="return confirm('آیا از حذف اطمینان دارید؟')"
                                                       href="{{route("contractor.panel.register_production.delete_sub_packing",[$contractor_allocation,0,$item->packing_form])}}">
                                                        <i class="fa fa-trash"></i>
                                                    </a>

                                                </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>

                                <br/>
                            </div>


                        </div>
                        <div class="col-md-12 center">
                            <form id="form1" autocomplete="off"
                                  action="{{route("contractor.panel.register_production.submit",[$contractor_allocation])}}"
                                  method="post"
                                  novalidate="novalidate">
                                @csrf
                                <a class="btn btn-outline-dark" style="width: 140px"
                                   href="{{route("contractor.panel.dashboard.view",$contractor_allocation)}}">

                                    بازگشت

                                </a>
                                @if(count($contractor_packing_list)>0)

                                    <button type="submit" class="btn btn-primary" onclick="return confirm('آیا از ثبت نهایی تولید اطمینان دارید؟')" style="width: 140px">
                                        ثبت نهایی تولید
                                    </button>


                                @endif

                            </form>
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
