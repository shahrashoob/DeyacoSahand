
@extends('layouts.admin._master')

@section('page_header_title',"کارتابل همکاری با ما/تایید پیش نویس اطلاعات مشتری")

@section('content')
    <div class="row">
        <div class="col-sm-12">


            <div class="card">
                <div class="card-header">
                    <h5>تایید پیش نویس اطلاعات مشتری</h5>
                </div>
                <div class="card-block">

                    <form id="form1" style="display: inline"
                          action="{{route("hr.employment.admin.customer.confirm_draft_information.submit",$employment)}}"
                          method="post"
                          novalidate="novalidate" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card " style="min-height: 700px">
                                    <div class="card-header" >
                                        <h5>اطلاعات مالی مشتریان</h5>
                                    </div>
                                    <div class="card-block">

                                        @include('hr.employment.admin.customer.confirm_draft_information._financial_info')
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="card " style="min-height: 700px">
                                    <div class="card-header" >
                                        <h5>اطلاعات تاییدیه مشتری</h5>
                                    </div>
                                    <div class="card-block">

                                        @include('hr.employment.admin.customer.drafting_contract_init._order_permission_list')
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card "style="min-height: 700px">
                                    <div class="card-header">
                                        <h5>اطلاعات پرداخت مشتری</h5>
                                    </div>
                                    <div class="card-block">
                                        @include('hr.employment.admin.customer.drafting_contract_init._payment_method_list')
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card " style="min-height: 500px">
                                    <div class="card-header">
                                        <h5>اطلاعات برگ خروج مشتری</h5>
                                    </div>
                                    <div class="card-block">

                                        @include('hr.employment.admin.customer.drafting_contract_init._exit_form_list')

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card " style="min-height:500px">
                                    <div class="card-header">
                                        <h5>اطلاعات فرم ورود مشتری</h5>
                                    </div>
                                    <div class="card-block">
                                        @include('hr.employment.admin.customer.drafting_contract_init._input_form_list')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-100"><br/></div>

                        <a href="{{route("hr.employment.admin.dashboard.view",$employment)}}"
                           class="btn btn-outline-dark btn-lg">بازگشت</a>
                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('آیا از تایید پیش نویس اطلاعات مشتری اطمینان دارید؟')"
                        >تایید</button>

                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

