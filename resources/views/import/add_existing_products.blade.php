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
                                <a href="{{route("import.add_existing_products.index")}}" class="btn btn-primary">بارگذاری
                                    مجدد</a>



                            @else
                                <a id="btn_update" href="{{route("import.add_existing_products.update")}}" class="btn btn-primary" >
                                    اعمال تغییرات</a>



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
                                <th>کد بسته بندی  </th>
                                <td>مقدار</td>
                                <th>نوع تراکنش</th>
                                <th>طرف حساب </th>
                            </tr>
                            @php $row=1;@endphp
                            @foreach($list as $item)
                                <tr class="{{$item->error==""?"":'alert-danger'}} {{$item->warning==""?"":'alert-warning'}}">
                                    <td>{{$item->id}}</td>
                                    <td>{{$item->warehouse->caption??""}}</td>
                                    <td>{{$item->packing_form_number??""}}</td>
                                    <td>{{$item->amount}}</td>
                                    <td>{{$item->trans_kind_item->caption??""}}</td>
                                    <td>{{$item->opp_kind_item->caption??""}}</td>
                                </tr>
                                @if($item->error!="" || $item->warning!="")
                                    <tr>
                                        <td colspan=8>
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
        $("#btn_update").click(function (){
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
    @include("component._spinner",["id"=>"#btn_update"])

@endsection
