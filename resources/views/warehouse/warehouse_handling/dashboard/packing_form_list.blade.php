@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])
@section("page_header_title","داشبورد انبار ")

@section('content')

    <div class="row">

        <div class="col-sm-12" id="panel_packing">
            <div class="card">
                <div class="card-header">
                    <h5> لیست بسته بندی ها در انبارگردانی {{$warehouse_handling->warehouse->caption}}
                        شماره: {{$warehouse_handling->getCode()}} </h5>
                </div>
                <div class="card-block">
                    @switch($status->id)
                        @case(524000401)
                            <div class="alert alert-warning">
                                <b>وضعیت {{$status->caption}}</b>
                            </div>
                            @break
                        @case(524000402)
                            <div class="alert alert-warning">
                                <b>وضعیت {{$status->caption}}</b>
                                لطفا در صورت تایید وضعیت بسته بندی ها، آنها را بررسی شده قرار دهید.
                            </div>
                            @break
                        @case(524000403)
                            <div class="alert alert-warning">
                                <b>وضعیت {{$status->caption}}</b>
                                <br/>
                                بسته بندی های زیر در انبارگردانی {{$warehouse_handling->warehouse->caption}}
                                خوانده نشده اند ولی در انبار موجود هستند.
                            </div>
                            @break
                        @case(524000404)
                            <div class="alert alert-warning">
                                <b>وضعیت {{$status->caption}}</b>
                                <br/>
                                بسته بندی های زیر در انبارگردانی {{$warehouse_handling->warehouse->caption}}
                                خوانده شده اند ولی در انباری به غیر از {{$warehouse_handling->warehouse->caption}}
                                وجود دارند.
                            </div>
                            @break
                        @case(524000405)
                            <div class="alert alert-warning">
                                <b>وضعیت {{$status->caption}}</b>
                                <br/>
                                بسته بندی های زیر در انبارگردانی {{$warehouse_handling->warehouse->caption}}
                                خوانده شده اند ولی بسته بندی با این کدها در سامانه وجود ندارد.
                            </div>
                            @break
                        @case(524000406)
                            <div class="alert alert-warning">
                                <b>وضعیت {{$status->caption}}</b>
                                <br/>
                                بسته بندی های زیر در انبارگردانی {{$warehouse_handling->warehouse->caption}}
                                خوانده شده اند ولی وضعیت موجود بودن آنها در انبار نامعتبر است.
                            </div>
                            @break
                        @case(524000407)
                            <div class="alert alert-warning">
                                <b>وضعیت {{$status->caption}}</b>
                                <br/>
                                بسته بندی های زیر در انبارگردانی {{$warehouse_handling->warehouse->caption}}
                                خوانده نشده اند ولی وضعیت موجود بودن آنها در انبار نامعتبر است.
                            </div>
                            @break

                    @endswitch
                    @include("warehouse.warehouse_handling.dashboard._packing_form_list",["action_packing_form"=>true])
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>

                <div class="center">
                    <a href="{{route("wh.warehouse_handling.dashboard.view",$warehouse_handling)}}"
                       class="btn btn-outline-dark">
                        بازگشت
                    </a>
                </div>


            </div>
        </div>


    </div>

@endsection
@section("styles")
    <style>

    </style>
@endsection
