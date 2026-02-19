

@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="card " style="min-height: 500px">
                        <div class="card-header" >
                            <h5>اطلاعات برگ ورود از انبار</h5>
                        </div>
                        <div class="card-block">
                            @include('hr.employment.admin.contractor.drafting_contract_init._input_form_list')

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card " style="min-height: 500px">
                        <div class="card-header">
                            <h5>اطلاعات برگ خروج از انبار</h5>
                        </div>
                        <div class="card-block">

                            @include('hr.employment.admin.contractor.drafting_contract_init._exit_form_list')

                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header">
                    <h5>تایید اولیه  قرارداد هوشمند
                        {{$contract->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1" style="display: inline"
                          action="{{route("hr.employment.admin.contractor.drafting_contract_init.submit",$employment)}}"
                          method="post"
                          novalidate="novalidate" autocomplete="off">
                        @csrf

                        @include("hr.employment.admin.contractor.drafting_contract_init._info")

                        <div class="w-100"><br/></div>

                        <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}"
                           class="btn btn-outline-dark btn-lg">بازگشت</a>
                        <button type="submit" class="btn btn-primary">تایید</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

