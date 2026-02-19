@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت تعویض روغنی")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5> {{ $customer->shop_name }}

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive center">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>شماره پلاک </th>
                                <th>مالک</th>
                                <th>موبایل</th>
                                <th>تعداد سرویس  </th>
                                <th>مبلغ کل (تومان)</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>{{$item->getPlaque()}}</td>
                                    <td>{{$item->firstname." ".$item->lastname}}</td>
                                    <td>{{$item->mobile}}</td>
                                    <td>{{$item->getCountService($start_date_time,$end_date_time)}}</td>
                                    <td>{{$item->getCountPriceService($start_date_time,$end_date_time)}}</td>


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
                <div class="text-center" >
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
                <div class="col-md-12" style="text-align: center">
                    <br/>
                    <a href="{{route("oil_change.oil.report.index")}}" class="btn btn-dark btn-lg">بازگشت</a>
                </div>
            </div>
            <div>

            </div>
        </div>

    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")

    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script src="{{asset('assets/plugins/jquery-validation-1.11.1/localization/messages_fa2.js')}}"></script>

    <script>

        $('#form1').validate({
            rules: {
                alphabet_id: "required",
                city_id: {minlength: 2, maxlength: 2, number: true, required: true},
                number1: {minlength: 3, maxlength: 3, number: true, required: true,},
                number2: {minlength: 2, maxlength: 2, number: true, required: true},
                current_km: {required: true},
            }
        });
        $("#city_id").on("input", function () {
            var dInput = this.value;
            if (this.value.length >= 2) {
                $("#number1").focus();
                $("#number1").val("");
            }
        });
        $("#number1").on("input", function () {
            var dInput = this.value;
            if (this.value.length >= 3) {
                $("#alphabet_id").focus();
                $("#alphabet_id").val("");
            }
        });
        $("#alphabet_id").on('change', function (e) {
            var dInput = this.value;
            if (this.value.length >= 1) {
                $("#number2").focus();
                $("#number2").val("");
            }
        });
        $("#number2").on("input", function () {
            var dInput = this.value;
            if (this.value.length >= 2) {
                $("#current_km").focus();
            }
        });
    </script>
@endsection
