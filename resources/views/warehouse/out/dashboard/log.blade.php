@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  تحویل انبار  ")
@php $permission_confirm=$post_user->checkButtonPermission("wh.out.dashboard.confirm_and_checkout");@endphp
@section('content')

@if(count($product_request_form->get_log_with_status())>0)
    <div class="row">
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h5> سابقه عملیات بر روی درخواست {{$product_request_form->getCode()}}</h5>
            </div>
            <div class="card-block">

                <div class="table-responsive">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>تاریخ و ساعت</th>
                            <th>اقدام کننده</th>
                            <th>رویداد</th>
                            <th>وضعیت</th>
                            <th>فرم انبار</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=$list->firstItem();@endphp
                        @foreach($list as $item)
                            <tr>
                                <td title="{{$item->id}}">{{$row++}}</td>

                                <td>{{$item->get_datetime()}}</td>
                                <td>{{$item->worker->fullname()}}</td>
                                <td>
                                    @if(isset($item->json_data_list_id))
                                        @if($post_user->checkButtonPermission("wh.out.dashboard.show_json_data"))

                                            <a href="{{route("wh.out.dashboard.show_json_data",[$product_request_form,$item->id,$page])}}" class="">
                                                {{$item->event->caption??""}}
                                            </a>
                                        @else
                                            {{$item->event->caption??""}}
                                        @endif
                                    @else
                                        {{$item->event->caption??""}}
                                    @endif

                                </td>
                                <td>{{$item->status->caption}}</td>
                                <td>
                                    @if($item->form)
                                        <a target="_blank"
                                           href="{{route("DCEF_QR",[$item->form,$item->form->getRandom()])}}">{{$item->form->code??""}}</a>
                                    @endif
                                </td>
                            </tr>

                            @if(isset($item->message->text))
                                <td colspan="6" style="padding: 0">
                                    <div class="alert alert-info ">
                                        {!! $item->message->text??"" !!}
                                    </div>
                                </td>
                                </tr>
                            @endif

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
    </div>
    <div class="col-md-12 center">
        <a class="btn btn-outline-dark"
           href="{{route("wh.out.dashboard.view",[$product_request_form,$page])}}">

            بازگشت</a>

    </div>
    </div>
@endif

@endsection
