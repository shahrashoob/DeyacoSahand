@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">
        @if($allow_edit)
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <h5>
                            ویرایش وضعیت حامل {{$carrier->getCaption()}}
                        </h5>
                    </div>
                    <div class="card-block">
                        <form id="form1" action="{{route("line_product_station.carrier.update",$carrier)}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="w-100"></div>
                                <div class="col-md-6">
                                    @include("component.input._aotocomplet2",[
                                        "id"=>"status_id",
                                        "label"=>"وضعیت حامل ",
                                        "option"=>$status_option["items"],
                                        "val"=>$status_option["value"],
                                        "text"=>$status_option["text"],
                                        "class_col"=>""
                                        ])
                                </div>
                            </div>

                            <a href="{{route("line_product_station.carrier.index")}}" class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary"> ذخیره</button>

                        </form>
                    </div>

                </div>

            </div>
        @endif
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        آخرین تغییرات
                    </h5>
                </div>
                <div class="card-block">
                    <table class="table table-styling center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th> کاربر</th>
                            <th>تاریخ</th>
                            <th>رویداد</th>
                            <th>وضعیت</th>
                            <th>کالا</th>
                            <th>بسته بندی</th>
                            <th>ماشین</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=0;@endphp
                        @foreach($list as $item)
                            <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                <td>{{++$row}}</td>
                                <td>{{$item->user->fullname()}}</td>
                                <td>{{$item->create_date()}}</td>
                                <td>
                                    {{$item->event->caption??""}}
                                </td>
                                <td>
                                    {{$item->status->caption??""}}
                                </td>
                                <td>{{$item->product->caption??""}}</td>
                                <td>{{$item->packing_form->code??""}}</td>
                                <td>{{$item->machine->caption??""}}</td>


                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                    <a href="{{route("line_product_station.carrier.index")}}" class="btn btn-outline-dark">بازگشت</a>

                </div>

            </div>

        </div>

        @endsection
        @section("styles")
            <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
            <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
        @endsection

        @section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "status_id_auto": "required",
            }
        });
    </script>
@endsection

