@extends('layouts.admin._master')
@section("page_header_title"," داشبورد بسته بندی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            @include("goods_kind_process.fabric_raw.packing.fabric_waiting_for_packing._search_view",["route"=>"fabric_raw.packing.fabric_waiting_for_packing.index"])
            <div class="card">
                <div class="card-header">
                    <h5>لیست تکه پارچه های در انتظار بسته بندی
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling" style="text-align: center!important;">
                            <thead>
                            <tr>

                                <th>#</th>
                                <th>شماره ردیف</th>
                                <th>محصول</th>
                                <td> درجه محصول</td>
                                <td>وضعیت</td>
                                <td>متراژ سیستم</td>
                                <td>متراژ کنترل کیفیت</td>
                                <td>متراژ نهایی</td>
                                <td>حامل</td>
                                <td>لات (همبافت)</td>

                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        <a href="{{route("fabric_raw.packing.fabric_waiting_for_packing.view",$item)}}">
                                            {{$item->code}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$item->product->code??""}} - {{$item->product->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->degree->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->status->caption??""}}
                                    </td>
                                    <td>
                                        {{$item->amount}}
                                    </td>
                                    <td>
                                        {{$item->amount_after_control}}
                                    </td>
                                    <td>
                                        {{$item->final_amount}}
                                    </td>
                                    <td>
                                        {{isset($item->packing_form_item->packing_form->carrier)?$item->packing_form_item->packing_form->carrier->getCaption():""}}
                                    </td>
                                    <td>
                                        {{$item->lot_number->code??""}}
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

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
