@extends('layouts.admin._master')


@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">

                        <h3 class="col-md-6">
                            <span class="badge  badge-danger">تعداد خطا: {{$error_count}}</span>
                        </h3>

                        <div style="text-align: center" class="col-md-12">

                            <br/>
                            <br/>
                            @if($error_count>0)
                                <div class="alert alert-danger">لطفا پس از رفع خطا های موجود دوباره فایل اکسل را
                                    بارگذاری کنید.
                                </div>
                                <a href="{{route("import.packing_form_handling.index")}}" class="btn btn-primary">بارگذاری
                                    مجدد</a>



                            @else
                                @if($worker->default_label_printer)
                                    <div class="alert alert-info">
                                        در صورت چاپ برچسب های بسته بندی، برچسب ها برای
                                        <b>{{$worker->default_label_printer->caption??""}}</b>
                                        ارسال می گردد.
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        لطفا لیبل پرینتر پیش فرض را انتخاب نمایید.
                                    </div>
                                @endif


                                @if($worker->default_label_printer)
                                    <div class="btn-group mb-2 mr-2 ">

                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">ایجاد
                                            بسته بندی های جدید
                                        </button>
                                        <div class="dropdown-menu " x-placement="top-start"
                                             style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(10px, -151px, 0px);">
                                            <a class="dropdown-item btn_action"
                                               href="{{route("import.packing_form_handling.update",0)}}">ایجاد بسته بندی
                                                ها بدون چاپ برچسب</a>
                                            <a class="dropdown-item btn_action"
                                               href="{{route("import.packing_form_handling.update",1)}}">ایجاد بسته بندی
                                                ها همراه با چاب برچسب</a>
                                        </div>

                                    </div>
                                @endif




                            @endif


                        </div>
                    </div>
                    <br/>
                    <br/>
                    <h5 style="text-align: center"> محتوای فایل اکسل</h5>
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <tr>
                                <th>ردیف فایل اکسل</th>
                                <th>انبار</th>
                                <th>کد محصول</th>
                                <th>نام محصول</th>
                                <th>کد لات</th>
                                <th>کد درجه</th>
                                <th>نوع بسته بندی</th>
                                <th>شماره ردیف بسته بندی</th>

                                <th>مقدار</th>
                                <th>مقدار فرعی</th>

                                <th>حامل</th>
                                <th>طرف حساب</th>
                            </tr>
                            @php $row=1;@endphp
                            @foreach($list as $item)
                                <tr class="{{$item->error==""?"":'alert-danger'}} {{$item->warning==""?"":'alert-warning'}}">
                                    <td>{{$item->id}}</td>
                                    <td>{{$item->warehouse->caption??""}}</td>
                                    <td>{{$item->product->code??""}}</td>
                                    <td>{{$item->product->caption??""}}</td>
                                    <td>{{$item->lot_number->code??""}} </td>
                                    <td>{{$item->degree->code??""}} </td>
                                    <td>{{$item->packing_type->caption??""}}</td>
                                    <td>{{$item->packing_form_number}}</td>

                                    <td>{{$item->amount}}</td>
                                    <td>{{$item->sub_amount}}</td>

                                    <td>{{isset($item->carrier)?$item->carrier->getCaption():""}}</td>

                                    <td>{{$item->opp_kind_item->caption??""}}</td>
                                </tr>
                                @if($item->error!="" || $item->warning!="")
                                    <tr>
                                        <td colspan=10>
                                            {!!$item->error!!}   {!!$item->warning!!}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                        </table>
                    </div>

                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$list->firstItem()}}</b>
                        تا
                        <b>{{$list->lastItem()}}</b>
                        از
                        <b>{{$list->total()}}</b>
                        رکورد موجود


                    </div>

                    <div class="text-center">
                        <br/>
                        <br/>
                        {{$list->links('pagination::bootstrap-4')}}
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section("styles")

    @include("component.smartwizard.script")
@endsection
@section("scripts")
    <script>
        $("#btn_update").click(function () {
            if ($(this).hasClass("disabled")) {
                return false;
            }
            if (confirm(" آیا از ثبت اطمینان دارید؟ ")) {
                $(this).addClass("disabled");
                return true;
            }
            return false;
        })

    </script>
    @include("component._spinner",["id"=>".dropdown-item"])
@endsection
