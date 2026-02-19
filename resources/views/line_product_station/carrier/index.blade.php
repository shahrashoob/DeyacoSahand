@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>
                      داشبورد حامل ها
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1" action="{{route("line_product_station.carrier.search")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"code",'label'=>"کد حامل  ","value"=>$code])

                        </div>

                        <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary">  جستجو</button>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th> کد</th>
                                <th> نوع حامل</th>
                                <th> وضعیت</th>
                                <th>کالای موجود در حامل</th>
                                <th>توضیحات</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{ route("line_product_station.carrier.edit",$item)}}">{{$item->code}}</a>
                                    </td>
                                    <td>
                                        {{$item->carrier_type->caption??""}}
                                    </td>
                                    <td>{{$item->status->caption??""}}</td>
                                    <td>
                                        @foreach($item->product as $item_product)
                                            {{$item_product->product->fullCaption()}}
                                        @endforeach
                                    </td>
                                    <td>
                                        {{$item->log_message}}
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
