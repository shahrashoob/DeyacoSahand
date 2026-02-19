@extends('layouts.admin._master')
@section("page_header_title","داشبورد جاری فروش ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
{{--            @include("sales.public._search_view",["route"=>"sales.dashboard.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست سفارش های {{$customer->caption??""}}</h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد سفارش</th>
                                <th>نام مرکز</th>
                                <th>کانال توزیع</th>
                                <th> استان</th>
                                <th>تعداد روز در<br/> انتظار ارسال</th>
                                <th> اولویت سفارش</th>
                                <th> وضعیت</th>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("sales.dashboard.view_order",$item->id)}}">{{$item->code()}}</a>
                                    </td>
                                    <td>{{$item->customer->caption??""}}</td>
                                    <td>{{$item->customer->channelType->caption??""}}</td>
                                    <td>{{$item->customer->province->caption??""}}</td>
                                    <td>{{$item->number_of_days_waiting()}}</td>
                                    <td>{{$item->priority->caption??""}}</td>
                                    <td>
                                        <a href="{{route("sales.dashboard.log",$item->id)}}">{{$item->getStatus(1)}}</a>
                                    </td>


                                </tr>
                            @endforeach
                            </tbody>

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
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

      <div class="col-md-12" style="text-align: center">
          @if(isset($back_url))
              <a href="{{$back_url}}"  class="btn btn-outline-dark"   type="button">
                  <i class="fa fa-arrow-right"></i> بازگشت
              </a>
          @else
              <a href="{{url()->previous()}}"  class="btn btn-outline-dark"   type="button">
                  <i class="fa fa-arrow-right"></i> بازگشت
              </a>
          @endif
      </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
