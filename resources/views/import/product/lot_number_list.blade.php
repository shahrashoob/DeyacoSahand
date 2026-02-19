@extends('layouts.admin._master')


@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فایل همبافت محصولات </h5>

                </div>
                <div class="card-body">
                    <div class="row">
                        @if($error_count>0)
                            <h3 class="col-md-6">
                                <span class="badge  badge-danger">تعداد خطا: {{$error_count}}</span>
                            </h3>
                        @else
                            <div class="alert alert-danger col-md-12">
                                <b>هشدار :</b>
                                در صورت اعمال بروز رسانی تمامی ردیف های لات که قبلا تعریف نشده باشد در سامانه تعریف می شوند.
                            </div>
                        @endif


                        <div style="text-align: center" class="col-md-12">

                            <br/>
                            <br/>
                            @if($error_count>0)
                                <div class="alert alert-danger">لطفا پس از رفع خطا های موجود دوباره فایل اکسل را
                                    بارگذاری کنید.
                                </div>
                                    <a href="{{route("import.product.lot_number.index")}}" class="btn btn-primary">بارگذاری
                                        مجدد</a>

                            @else
                                <form action="{{route("import.product.lot_number.update")}}" method="post">
                                    @csrf
                                    <button class="btn btn-primary" type="submit">اعمال بروزرسانی</button>
                                </form>
                            @endif

                        </div>
                    </div>
                    <br/>
                    <br/>
                    <h5 style="text-align: center"> محتوای فایل اکسل</h5>
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <tr>
                                <th>#</th>
                                <th> کد محصول </th>
                                <th>کد لات(همبافت)(بچ)</th>
                                <th>کد نوسا</th>
                            </tr>
                            @php $row=1;@endphp
                            @foreach($list as $item)
                                <tr class="{{$item->error==""?($item->warning!=""?"alert_warning":""):'alert-danger'}}">
                                    <td>{{$row++}}</td>
                                    <td>{{$item->product_code}}</td>
                                    <td>{{$item->code}}</td>
                                    <td>{{$item->nosa_code}}</td>
                                </tr>
                                @if($item->error!="")
                                    <tr>
                                        <td colspan=8>
                                            {!!$item->error!!}
                                        </td>
                                    </tr>
                                @endif
                                @if($item->warning!="")
                                    <tr>
                                        <td colspan=8>
                                            {!!$item->warning!!}
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
