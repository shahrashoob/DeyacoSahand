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

                    <div class="alert alert-warning">
                        با توجه به اینکه مقدار کالای(های) موجود در بسته بندی (های) انتخاب شده بیش از مقدار درخواست می
                        باشد، لذا باید درخواست (های) دیگری را همراه با این درخواست تحویل نمایید.

                        <br/>
                        لیست کالا هایی که باید درخواست دیگری برای آنها انتخاب نمایید:
                        <br/>
                        @foreach($product_list as $item)
                            {{$item->caption}} <br/>
                        @endforeach
                        لیست سایر درخواست های همین درخواست کننده به شرح ذیل می باشد.
                    </div>


                    <form id="form1"
                          action="{{route("wh.out.delivery.submit_select_other_product_request_form_item",[$product_request_form,$page,$dashboard_type])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">

                            @include("warehouse.out.delivery._selected_packing_list")

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling center" style="">
                                        <thead>
                                        <tr>
                                            <th colspan="10">
                                                لیست سایر درخواست های درخواست کننده
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>ردیف</th>
                                            <th>
                                                <input type="checkbox" id="check_all">
                                            </th>
                                            <th>شماره درخواست</th>
                                            <th>کد کالا</th>
                                            <th> عنوان کالا</th>
                                            <th> عنوان درجه</th>
                                            <th> واحد سنجش</th>
                                            <th> خط ورودی</th>
                                            <th> مقدار درخواست</th>
                                            <th> مقدار تحویل شده</th>
                                            <th> مقدار باقی مانده</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @php $plist=[];@endphp
                                        @foreach($product_request_form_item_list as $item)
                                            <tr>
                                                <td>{{++$row}}</td>
                                                <td>

                                                    @if(!isset($plist[$item->product_request_form->id]))
                                                        <input type="checkbox" class="prf_item"
                                                               name="data[product_request_form][{{$item->product_request_form->id}}]"
                                                        >
                                                        @php $plist[$item->product_request_form->id]=1;@endphp
                                                    @else

                                                    @endif

                                                </td>
                                                <td>
                                                    {{$item->product_request_form->code}}
                                                </td>
                                                <td>{{$item->product->code}}</td>
                                                <td>{{$item->product->caption}}</td>
                                                <td>{{$item->degree->caption}}</td>
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
                            @if($dashboard_type=="customer")
                            <a href="{{route("wh.out.customer.view",[$product_request_form->order->customer_id])}}?page={{$page}}"
                               class="btn btn-outline-dark">بازگشت</a>
                            @else
                                <a href="{{route("wh.out.delivery.index",[$product_request_form,0,$page])}}"
                                   class="btn btn-outline-dark">بازگشت</a>
                            @endif
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
<script>
    $("#check_all").click(function () {

        if ($(this).is(":checked")) {
            $(".prf_item").click();

        } else {
            $(".prf_item").click();



        }
    })
</script>
@endsection



