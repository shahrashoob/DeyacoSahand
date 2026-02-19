@extends('layouts.admin._master')
@section("page_header_title","داشبورد مشتریان ")

@section("content")
    <div class="row" style="text-align: center">
        <div class="col-md-12">

            <h3>
                با توجه به اینکه شما یک (یا چند) درخواست  <b class="text-success"> در انتظار تایید پیش نویس </b> دارید، لطفا ابتدا آنها را
                نهایی کنید.
            </h3>


        </div>

        <div class="w-25"></div>
        <div class="col-sm-12 col-md-6 col-md-offset-3">
            <br/><br/>
            <br/>
            <div class="card">

                @foreach($list as $item)

                    <div class="card-block border-bottom">
                        <div class="row d-flex align-items-center">

                            <div class="col">
                                <h3 class="f-w-300">
                                    <a href="{{route("customer_group.buy.index",$item)}}" class="btn-check">
                                       تکمیل سفارش  {{$item->code()}}

                                    </a>
                                </h3>
                                <span class="d-block text-uppercase"> ثبت سفارش توسط   <b>{{$item->register_user->fullname()}}</b></span>
                            </div>
                        </div>
                    </div>

                @endforeach


            </div>
        </div>
    </div>
@endsection
