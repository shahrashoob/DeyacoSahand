@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> افزودن بند جدید
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("accounting.contract.clause.submit_clause_article",$clause_type)}}"
                          method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                @include("component.input._textarea",["id"=>"caption",'label'=>"بند قراداد","value"=>""])

                            </div>
                            <div class="col-md-6">

                                @include("component.input._select",[
                                         "id"=>"token_id1",
                                         "label"=>"token1",
                                         "option"=>$contract_keyword_option["items"],
                                        ]) <br/>
                                @include("component.input._select",[

                                     "id"=>"token_id2",
                                     "label"=>"token2",
                                     "option"=>$contract_keyword_option["items"],
                                    ]) <br/>
                                @include("component.input._select",[

                                     "id"=>"token_id3",
                                     "label"=>"token3",
                                     "option"=>$contract_keyword_option["items"],
                                    ]) <br/>
                                @include("component.input._select",[

                                     "id"=>"token_id4",
                                     "label"=>"token4",
                                     "option"=>$contract_keyword_option["items"],
                                    ]) <br/>
                                @include("component.input._select",[

                                     "id"=>"token_id5",
                                     "label"=>"token5",
                                     "option"=>$contract_keyword_option["items"],
                                    ])

                                @include("component.input._select",[
                                         "id"=>"token_id6",
                                         "label"=>"token6",
                                         "option"=>$contract_keyword_option["items"],
                                        ]) <br/>
                                @include("component.input._select",[

                                     "id"=>"token_id7",
                                     "label"=>"token7",
                                     "option"=>$contract_keyword_option["items"],
                                    ]) <br/>
                                @include("component.input._select",[

                                     "id"=>"token_id8",
                                     "label"=>"token8",
                                     "option"=>$contract_keyword_option["items"],
                                    ]) <br/>
                                @include("component.input._select",[

                                     "id"=>"token_id9",
                                     "label"=>"token9",
                                     "option"=>$contract_keyword_option["items"],
                                    ]) <br/>
                                @include("component.input._select",[

                                     "id"=>"token_id10",
                                     "label"=>"token10",
                                     "option"=>$contract_keyword_option["items"],
                                    ])
                            </div>
                        </div><br/>
                        <a href="{{route('accounting.contract.clause.index')}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> افزودن</button>

                    </form>

                </div>
            </div>
        </div>

    </div>
    @include('accounting.contract.clause._list_clause_article')
@endsection

@section("styles")

    {{--    @include("component.input.datepicker._script")--}}
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",

            }
        });
    </script>
@endsection
