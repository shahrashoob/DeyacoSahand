@extends('layouts.admin._master')

@section('page_header_title'," داشبورد روابط عمومی")

@section('content')
    <div class="row">
        <div class="col-sm-12">


            <form id="form1" action="{{route("utility.pup_up.admin.store")}}"
                  method="post"
                  autocomplete="off"
                  novalidate="novalidate">
                @csrf
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5> افزودن Pup Up جدید</h5>
                            </div>
                            <div class="card-block">
                               @include("utility.pup_up.admin._pup_up_info")
                            </div>
                        </div>
                    </div>

                </div>


                <a href="{{route("utility.pup_up.admin.index")}}"
                   class="btn btn-outline-dark">بازگشت</a>

                <button type="submit" class="btn btn-primary"> افزودن</button>

            </form>

        </div>
    </div>
    </div>

    </div>

@endsection

@section("styles")
    @include("component.input.tiny._script")
    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>

@endsection

@section("scripts")
    @include("component.script_function.get_new_option")

    <script>

        $('#form1').validate({
            rules: {
                caption: "required",
                number_of_show: "required",
                start_date_value: "required",
                end_of_date_value: "required",

            }
        });

    </script>
@endsection
