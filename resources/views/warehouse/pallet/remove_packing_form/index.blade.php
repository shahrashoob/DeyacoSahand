@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])

@section('page_header_title',"داشبورد  انبار  ")

@section('content')

    <div class="row">


        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>حذف بسته بندی از پالت {{$pallet->code}}</h5>
                </div>

                @include("component.input._hidden",["id"=>"packing_form_read_ids","value"=>json_encode($packing_form_read_ids)])


                <div class="card-block">
                    <div class="row">
                        <div class="col-md-12 center">
                            <form id="form_api">

                                <h5>تعداد بسته بندی انتخاب شده <span class="badge badge-secondary"
                                                                     id="sum_of_confirm_packing_form">{{count($packing_form_read_ids)}}</span>
                                </h5>

                                <h5>آخرین بسته بندی انتخاب شده <span class="badge badge-secondary"
                                                                     id="last_packing_form">---</span>
                                </h5>
                                <h5> <span class="badge text-danger"
                                           id="packing_not_register"></span>
                                </h5>
                                <div class="col-md-12 col-sm-12 " style="margin-top: 10px">
                                    {{$allow_entry_with_pin?"کد پین":"کد بسته بندی"}}
                                </div>
                                <h5 class="text-danger">
                                    <span id="alert_danger_select"></span>
                                </h5>

                                <input id="packing_code" autofocus type="text"
                                       style="width: 140px;height: 33px;margin-bottom: 15px"
                                       value="">
                                <br/>
                                <button type="submit" class="btn btn-primary btn-sm" id="submit_packing_code"
                                        style="width: 140px">بررسی
                                    و
                                    ثبت
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <div class="col-md-12 center" id="btn_submit">
            <a href="{{route("wh.pallet.dashboard.index",$pallet)}}"
               class="btn btn-outline-dark">بازگشت</a>
            <a type="submit" class="btn btn-primary submit_form"
               href="{{route("wh.pallet.remove_packing_form.show_list",$pallet)}}"> تایید و
                ادامه</a>
        </div>

    </div>

@endsection
@section("styles")

@endsection


@section("scripts")
   @include("component.read_packing_form._script",["message_type_id"=>405])
@endsection

