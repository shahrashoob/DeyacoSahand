@extends('layouts.admin._master')


@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <h3 class="col-md-6">
                            <span class="badge badge-success">تعداد رکورد: {{$count}}</span><br/>

                        </h3>
                        <h3 class="col-md-6">

                            <span class="badge  badge-danger">تعداد خطا: {{$error_count}}</span>
                        </h3>
                        <h3 class="col-md-12">

                            <span class="badge  badge-info">تعداد بسته بندی اصلی: {{$parent_packing_count}}</span>
                        </h3>
                        <h3 class="col-md-12">

                            <span class="badge  badge-info">تعداد بسته بندی فرعی: {{$packing_count}}</span>
                        </h3>

                        <div style="text-align: center" class="col-md-12">

                            <br/>
                            <br/>
                            @if($error_count>0)
                                <div class="alert alert-danger">لطفا پس از رفع خطا های موجود دوباره فایل اکسل را
                                    بارگذاری کنید.
                                </div>
                                    <a href="{{route("contractor.panel.upload_packing_form.index",$contractor_allocation)}}" class="btn btn-primary">بارگذاری
                                        مجدد</a>



                           @else
                            <a href="{{route("contractor.panel.upload_packing_form.upload_product",$contractor_allocation)}}" class="btn btn-primary">
                                اعمال تغییرات</a>

                        </a>

                            @endif


                        </div>
                    </div>
                    <br/>
                    <br/>
                    <h5 style="text-align: center"> محتوای فایل اکسل</h5>
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <tr class="center">
                                <th>ردیف </th>
                                <th>ردیف بسته بندی اصلی  </th>
                                <th>شماره حامل بسته بندی اصلی </th>
                                <th>ردیف بسته بندی فرعی </th>
                                <th> شماره حامل بسته بندی فرعی </th>
                                <th> درجه</th>
                                <th>کد لات</th>
                                <th>مقدار اصلی</th>
                                <th>مقدار فرعی</th>
                                <th>مقدار فرعی2</th>
                            </tr>
                            @php $row=1;@endphp
                            @foreach($list as $item)
                                <tr class="{{$item->error==""?"":'alert-danger'}} center">
                                    <td>{{$row++}}</td>
                                    <td>{{$item->parent_packing_form_row}}</td>
                                    <td>{{$item->parent_carrier->code??""}} ({{$item->parent_carrier->carrier_type->caption??""}})</td>
                                    <td>{{$item->packing_form_row}}</td>
                                    <td>{{$item->carrier->code??""}} ({{$item->carrier->carrier_type->caption??""}})</td>
                                    <td>{{$item->degree->caption??""}}</td>
                                    <td>{{$item->lot_number_code}}</td>
                                    <td>{{$item->amount}}</td>
                                    <td>{{$item->sub_amount}}</td>
                                    <td>{{$item->sub_amount2}}</td>
                                </tr>
                                @if($item->error!="")
                                    <tr>
                                        <td colspan=10 >
                                            {!!$item->error!!}
                                        </td>
                                    </tr>
                                @endif
                                @if($item->warning!="")
                                    <tr>
                                        <td colspan=10 class="alert-warning">
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

@section("styles")

@include("component.smartwizard.script")
@endsection
