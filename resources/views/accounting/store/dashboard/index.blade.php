@extends('layouts.admin._master')
@section('page_header_title'," کارتابل مالی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست کالا های فروشگاه</h5>
                </div>
                <div class="card-block">
                    <div class="row">
                        @foreach($list as $item)
                            <div class="col-sm-12 col-md-4 text-center">
                                <h5>{{$item->caption}}</h5>
                                <hr>
                                <div class="card text-center">
                                    <div class="card-body">
                                        <p class="card-text" style="text-align: justify">{{$item->description??""}}</p>
                                        <p style="font-weight: bold">

                                          @if($item->price == 0)
                                               رایگان
                                            @else
                                                قیمت:
                                                {{number_format($item->price)}} ریال
                                            @endif

                                        </p>
@if($item->id!=4)
                                            حداقل ورژن:
                                            {{$item->min_version}}
    <br/>
@endif

<br/>
                                        @if($item->status_id==4500001)

                                            <form id="form1"
                                                  action="{{route('accounting.store.dashboard.create',$item)}}"
                                                  method="post"
                                                  novalidate="novalidate">
                                                @csrf
                                                @if($item->id ==4)
                                                    درخواست پشتیبانی برای
                                                    <input type="number" min="1" max="24" value="1"
                                                           name="months_number">
                                                    ماه
                                                    <br/>
                                                    تاریخ پایان پشتیبانی:
                                                    {{$support_end_date}}
                                                    <br/>
                                                @endif

                                                <button type="submit"
                                                        class="btn btn-primary">خرید
                                                </button>
                                            </form>
                                                @elseif($item->status_id==4500002)
                                                    @if($item->other_id==0)
                                                        <a href="#"
                                                           onclick="alert('لطفا برای انجام تنظیمات با پشیتبانی تماس بگیرید.')"
                                                           class="btn btn-warning" style="color: white">خریداری شده در
                                                            انتظار
                                                            انجام تنظیمات</a>
                                                    @else
                                                        <a href="{{route('accounting.store.store_setting.store1.create',[$item,$item->other_id])}}"
                                                           class="btn btn-warning" style="color: white">خریداری شده در
                                                            انتظار
                                                            انجام تنظیمات</a>
                                                    @endif
                                                @else
                                                    <a class="btn btn-secondary" style="color: white">خریداری شده</a>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        @endsection
        @section("styles")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

