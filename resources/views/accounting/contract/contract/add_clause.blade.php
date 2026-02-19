@extends('layouts.admin._master')

@section('page_header_title'," کارتابل منابع انسانی ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن ماده جدید برای
                        {{$contract->caption}}
                    </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route('accounting.contract.contract.store_clause',$contract)}}"
                          method="post"
                          enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">

                                @include("component.input._select",[
                                          "id"=>'clause_type_id',
                                          "label"=>" ماده قراداد  ",
                                          "option"=>$clause_types_option["items"],

                              ])<br/>
                                <div class="col-md-12">
                                    <label>بند های ماده ی قراداد:</label><br/>
                                </div>
                                @foreach($clause_article_list as $item)

                                    <div class="col-md-6">
                                        <input type="checkbox" name="clause_article_id[{{$item->id}}]"
                                                {{($clause_article_list->count()==1)?"checked='checked'":""}}>
                                        {{$item->caption}}
                                    </div>
                                @endforeach

                                <br/>
                                @include("component.input._number",["id"=>"priority_number",'label'=>"اولویت ","value"=>""])
                            </div>

                        </div>
                        <br/>
                        <a href="{{route('accounting.contract.contract.index')}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> افزودن</button>

                    </form>

                </div>

            </div>
        </div>

    </div>
    @include('accounting.contract.contract._list')
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
                "clause_type_id_auto": "required",
                "priority_number": "required",
            }
        });
    </script>

    <script>
        document.getElementById('clause_type_id').addEventListener('change', function () {
            var clause_type_id = this.value;
            $("#priority_number").attr("readonly", true);
            window.location.href = "{{route('accounting.contract.contract.add_clause',$contract)}}/" + clause_type_id;
        });
    </script>

@endsection