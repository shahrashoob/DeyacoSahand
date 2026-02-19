@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مالی ")

@section('content')
    <div style="margin: auto; max-width: 500px">

        <div class="card">
            <div class="card-header">
                <h5 class="text-center">پیش فاکتور خرید
                    {{$store->caption}}
                </h5>
            </div>
            <div class="card-block">

                <form id="form1" action="{{route("accounting.store.dashboard.store",$store)}}" method="post"
                      novalidate="novalidate">
                    @csrf
                    <div class="row">
                        @include("component.input._hidden",["id"=>"price","value"=>$total_price])

                        <br/>
                        <div class="col-md-12">
                            <div class="table-responsive center">
                                <table class="table table-styling">
                                    <thead>
                                    <tr>
                                        <th>شرح خدمات</th>
                                        <th>قیمت</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>قیمت</td>
                                        <td> {{number_format($store->price_sum)}} ریال</td>
                                    </tr>
                                    <tr>
                                        <td>ارزش افزوده</td>
                                        <td> {{number_format($tax)}} ریال</td>
                                    </tr>
                                    <tr style="font-weight: bold">
                                        <td>مبلغ کل</td>
                                        <td> {{number_format($total_price)}} ریال</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <a href="{{route("accounting.store.dashboard.index")}}" class="btn btn-outline-dark">بازگشت</a>
                                @if($credit<$total_price && $permission_client_buy)
                                    <a href="{{route('accounting.client.buy.index')}}" class="btn btn-success text-white">افزایش اعتبار</a>
                                @endif

                                @include("component.input._hidden",["id"=>"months_number","value"=>$months_number??""])
                                <button type="submit" class="btn  btn-primary">تکمیل خرید</button>
                            </div>

                        </div>


                    </div>
                </form>

            </div>
        </div>


    </div>

@endsection

