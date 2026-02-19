@extends('layouts.admin._master')
@section("page_header_title","داشبورد تولید چله کشی ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5> چله کشی</h5>
                    @if( $post_user->checkButtonPermission("warps.production_form.implementation_period_form.index"))

                        <a href="{{route("warps.production_form.implementation_period_form.index")}}"
                           class="btn btn-success">
                            فرم تولید چله کشی (ویژه دوره پیاده سازی)
                        </a>
                    @endif
                </div>


            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
