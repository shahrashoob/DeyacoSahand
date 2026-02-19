@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل انبار  ")
@php $permission_confirm=$post_user->checkButtonPermission("wh.out.dashboard.confirm_and_checkout");@endphp
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> فرم درخواست کالا از انبار - کد {{$product_request_form->getCode()}} </h5>
                </div>

                <div class="card-block">


                    @include("warehouse.out.dashboard._small_info")


                    <form id="form1"
                          action="{{route("wh.out.check_packing_form.submit",[$product_request_form,$page,$dashboard_type])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        @include("component.input._text",["id"=>"code",'label'=>"کد بسته بندی (بدون DCPK)","value"=>"","class_col"=>"col-md-3"])


                        <div class="col-md-12">
                            <button type="submit" class="btn btn-info"
                            >
                                <i class="fas fa-question"></i>
                                استعلام بسته بندی
                            </button>
@if($dashboard_type=="customer")
                                <a href="{{route("wh.out.customer.view",$product_request_form->order->customer_id)."?page=".$page}}"
                                   class="btn btn-outline-dark">بازگشت</a>
                            @else
                                <a href="{{route("wh.out.dashboard.view",$product_request_form)."?page=".$page}}"
                                   class="btn btn-outline-dark">بازگشت</a>
@endif

                        </div>

                    </form>


                </div>


            </div>

        </div>

        @if(isset($list))
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> نتیجه استعلام بسته بندی {{$code??""}}  </h5>
                    </div>

                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>کد</th>
                                    <th>توضیحات</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($list as $item)
                                    <tr class="table-{{$item["alert"]}}">
                                        <th scope="row">{{$item["code"]}}</th>
                                        <td>{!! $item["message"] !!}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection
@section("styles")


@endsection


@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "unit_id": "required",
            }
        });
    </script>
@endsection
