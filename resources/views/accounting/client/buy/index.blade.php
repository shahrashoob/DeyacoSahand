@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مالی ")

@section('content')
    <div style="margin: auto; max-width: 500px">

        <div class="card">
            @if($message!="")
                <div class="alert alert-warning">{!! $message !!}</div>
            @endif
            <div class="card-header">
                <h5> شارژ حساب </h5>
            </div>
            <div class="card-block"  >

                <form id="form1" action="{{route("accounting.client.buy.store")}}" method="post"
                      novalidate="novalidate">
                    @csrf
                    <div class="row">
                        @include("component.input._number",["id"=>"amount",
                                                            "label"=>"مبلغ شارژ (ریال)",
                                                            "class_col"=>"col-md-12",
                                                            ])

                        <br/>
                        <div class="col-md-12">
                            <div class="alert alert-dark" role="alert">
                                <h4 class="alert-heading"> <i class="fas fa-exclamation-triangle"></i> توجه </h4>
                                <ul class="description-font-size pr-3  mb-0">
                                    {{--                                        <li class="mb-2 line-height-17">--}}
                                    {{--                                            9 درصد مالیات بر ارزش افزوده از میزان شارژ حساب شما کم می شود.--}}
                                    {{--                                        </li>--}}
                                    <li class="mb-2 line-height-17">
                                        لطفا در وارد کردن مبلغ شارژ دقت فرمایید.
                                    </li>
                                    <li class="mb-2 line-height-17">
                                        مبلغ را به ریال وارد نمایید.
                                    </li>

                                    <li class="line-height-17">
                                        حداقل شارژ {{number_format($min_of_charge)}} ریال می باشد.
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>



                    <a href="{{route("dashboard")}}" class="btn btn-outline-dark">بازگشت</a>

                    <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>

                </form>

            </div>
        </div>


    </div>

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                amount: {required: true, number: true, min: {{$min_of_charge}}, max: {{$max_of_charge}}}
            }
        });
    </script>
@endsection
