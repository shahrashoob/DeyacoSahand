@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل انبار  ")

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        تایید نهایی بسته های انتخاب شده
                    </h5>
                </div>

                <div class="card-block">

                    <form id="form1"
                          action="{{route("wh.out.delivery.confirm",[$product_request_form,$dashboard_type??""])}}"
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
                                                لیست اقلام درخواست (های) در حال تحویل
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>ردیف</th>
                                            <th>شماره درخواست</th>
                                            <th>کد کالا</th>
                                            <th> عنوان کالا</th>
                                            {{--                                            <th> عنوان درجه</th>--}}
                                            <th> واحد سنجش</th>
                                            <th> خط ورودی</th>
                                            <th> مقدار درخواست</th>
                                            <th> مقدار تحویل شده</th>
                                            <th> مقدار باقی مانده</th>
                                            <th> مقدار در حال تحویل</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach($product_request_form_item_list as $item)
                                            @if(isset($prf_item_value[$item->id]) && $prf_item_value[$item->id]!=0)
                                                <tr
                                                        @if($item->product_request_form_id == $product_request_form->id) class="alert-info" @endif
                                                >
                                                    <td>{{++$row}}</td>
                                                    <td>
                                                        {{$item->product_request_form->code}}
                                                    </td>
                                                    <td>{{$item->product->code}}</td>
                                                    <td>{{$item->product->caption}}</td>
                                                    {{--                                                    <td>{{$item->degree->caption}}</td>--}}
                                                    <td>{{$item->product->unit->caption}}</td>
                                                    <td>{{$item->input_line_code}}</td>
                                                    <td>{{$item->amount_request}}</td>
                                                    <td>
                                                        {{$item->amount_sent}}

                                                    </td>
                                                    <td>{{$item->amount_remaining}}</td>

                                                    <td>
                                                        {{$prf_item_value[$item->id]}}

                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>


                        <hr/>

                        @include("warehouse.out.delivery._confirm_action")
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
    @include("component._spinner",["id"=>".dropdown-item "])
    <script>
        $(".btn_confirm_print").click(function () {
            $("#print").val("print_" + $(this).data('id'));
            $("#form1").submit();
        })
        $("#btn_confirm_back").click(function () {
            $("#print").val("back");
            $("#form1").submit();
        });
        $('#form1').validate({
            rules: {
                "print_number": "required",
            }
        });
    </script>
@endsection




