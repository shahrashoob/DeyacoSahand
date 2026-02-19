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

                        <div style="text-align: center" class="col-md-12">

                            <br/>
                            <br/>
                            @if($error_count>0)
                                <div class="alert alert-danger">لطفا پس از رفع خطا های موجود دوباره فایل اکسل را
                                    بارگذاری کنید.
                                </div>
                                    <a href="{{route("accounting.tariff.upload",$tariff)}}" class="btn btn-primary">بارگذاری
                                        مجدد</a>



                           @else
                            <a id="btn_update" href="{{route("accounting.tariff.upload_product",$tariff)}}" class="btn btn-primary btn_action">
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
                            <tr>
                                <th>ردیف</th>
                                <th>کد محصول  </th>
                                <th>نام محصول  </th>
                                <th>نوع فروش  </th>
                                <th>کد خدمت  </th>
                                <th>درجه </th>
                                <th>نوع بسته بندی </th>
                                <th>انبار</th>
                                <th>قیمت </th>
                                <th>حداقل خرید </th>
                                <th> حداکثر خرید </th>
                                <th>به قیمت واحد فاکتور با <br/>توجه به راس پرداخت،<br/> n درصد به ازای <br/>هر روز اضافه شود</th>
                                <th>کد  کالای مشتری
                                </th>
                                <th>عنوان کالای مشتری
                                </th>
                            </tr>
                            @php $row=1;@endphp
                            @foreach($list as $item)
                                <tr class="{{$item->error==""?"":'alert-danger'}}">
                                    <td>{{$item->row_id}}</td>
                                    <td>{{$item->product_code}}</td>
                                    <td>{{$item->product->caption??""}}</td>
                                    <td>{{$item->type_of_sale_of_product->caption??""}} </td>
                                    <td>{{$item->service->code??""}} </td>
                                    <td>{{$item->degree->caption??""}}</td>
                                    <td>{{$item->packing_type->caption??""}}</td>
                                    <td>{{$item->warehouse->caption??""}}</td>
                                    <td>{{$item->fea}}</td>
                                    <td>{{$item->min_buy}}</td>
                                    <td>{{$item->max_buy}}</td>
                                    <td>{{$item->increase_percentage_deadline_per_day}}</td>
                                    <td>{{$item->customer_product_code}}</td>
                                    <td>{{$item->customer_product_caption}}</td>
                                </tr>
                                @if($item->error!="")
                                    <tr>
                                        <td colspan=5>
                                            {!!$item->error!!}
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
@include("component._spinner",["id"=>"#btn_update"])
@endsection
