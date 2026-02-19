@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        انتخاب سایر درخواست های کالا
                    </h5>
                </div>

                <div class="card-block">

                    @if(count($product_request_form_item_list) >1)
                    <div class="alert alert-warning">
                        با توجه به اینکه بسته بندی انتخاب شده بیش از مقدار درخواست می باشد،
                        لذا باید درخواست دیگری را همراه با این درخواست تحویل نمایید.
                    </div>
                    @endif


                    <form id="form1"
                          action="{{route("wh.out.delivery.submit_select_amount_of_product_request_form_variety_1",$product_request_form_item)}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">

                            <div class="col-md-12">
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th colspan="7">
                                            لیست بسته بندی های انتخاب شده
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th> کد بسته بندی</th>
                                        <th>نوع بسته بندی</th>

                                        <th>حامل</th>
                                        <th>تعداد آیتم</th>
                                        <th>تنوع آیتم</th>
                                        <th></th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=1;@endphp
                                    @foreach($selected_packing_list as $item)
                                        <tr>
                                            <td>{{$row++}}</td>

                                            <td>{{$item->getCode()}}</td>
                                            <td>{{$item->packing_type->caption??""}}</td>
                                            <td>{{$item->carrier->code??""}}</td>

                                            <td>{{$item->items()->count()}}</td>
                                            <td>{{$item->items()->distinct("product_id")->count("id")}}</td>

                                            <td>
                                                <a class="m-t-5 collapsed"
                                                   data-toggle="collapse" href="#packing_{{$item->id}}"
                                                   role="button" aria-expanded="false"
                                                   aria-controls="packing_{{$item->id}}">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">
                                                <div class="collapse " id="packing_{{$item->id}}" style="">
                                                    <div class="row " style="border: 3px solid #efefef">


                                                        <div class="table-responsive">
                                                            <table class="table table-styling">
                                                                <thead>
                                                                <tr>
                                                                    <th colspan="6">
                                                                        <h5>لیست آیتم های موجود در بسته بندی {{$item->code}}</h5>
                                                                    </th>
                                                                </tr>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>ردیف</th>
                                                                    <th>کد کالا</th>
                                                                    <th>نام کالا</th>
                                                                    <th>{{$item->getUnitCaption("unit","measurement")}}</th>
                                                                    <th>{{$item->getUnitCaption("sub_unit","measurement")}}</th>
                                                                </tr>

                                                                </thead>
                                                                <tbody>
                                                                @php $row=1;@endphp
                                                                @foreach($item->items as $packing_item)
                                                                    <tr>
                                                                        <td>{{$row++}}</td>
                                                                        <td>{{$packing_item->production_form_item->code??""}}</td>
                                                                        <td>{{$packing_item->product->code}}</td>
                                                                        <td>{{$packing_item->product->caption}}</td>
                                                                        <td>{{$packing_item->final_amount}}</td>
                                                                        <td>{{$packing_item->sub_amount}}</td>
                                                                @endforeach
                                                                </tbody>

                                                            </table>
                                                        </div>


                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling center" style="">
                                        <thead>
                                        <tr>
                                            <th colspan="10">
                                                لیست اقلام درخواست کالاها
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>ردیف</th>
                                            <th></th>
                                            <th>شماره درخواست</th>
                                            <th>کد کالا</th>
                                            <th> عنوان کالا</th>
                                            <th> واحد سنجش</th>
                                            <th> خط ورودی</th>
                                            <th> مقدار درخواست</th>
                                            <th> مقدار تحویل شده</th>
                                            <th> مقدار باقی مانده</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach($product_request_form_item_list as $item)
                                            <tr>
                                                <td>{{++$row}}</td>
                                                <td>
                                                    @if($item->id==$product_request_form_item->id)
                                                    <input type="checkbox"
                                                            checked  disabled >
                                                        <input type="hidden" name="data[product_request_form_item][{{$item->id}}]"
                                                               >
                                                    @else
                                                        <input type="checkbox"  name="data[product_request_form_item][{{$item->id}}]"
                                                               >
                                                    @endif

                                                </td>
                                                <td>
                                                    {{$item->product_request_form->code}}
                                                </td>
                                                <td>{{$item->product->code}}</td>
                                                <td>{{$item->product->caption}}</td>
                                                <td>{{$item->product->unit->caption}}</td>
                                                <td>{{$item->input_line_code}}</td>
                                                <td>{{$item->amount_request}}</td>
                                                <td>
                                                    {{$item->amount_sent}}

                                                </td>
                                                <td>{{$item->amount_remaining}}</td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>


                        <hr/>

                        <div style="text-align: center">
                            <a href="{{route("wh.out.delivery.index",$product_request_form_item)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary">ثبت و ادامه</button>

                        </div>
                    </form>
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

@endsection



