@extends('layouts.admin._master',["keypress_enable"=>1])
@section('page_header_title',"داشبورد  بافندگی")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header"><h5> تایید تحویل مواد اولیه از انبار
                        برای {{$machine->warehouse->caption??""}}</h5></div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route($route_path."submit",$machine)}}"
                          method="post" autocomplete="off" novalidate="novalidate"> @csrf
                        @include("warehouse.production_warehouse.dashboard._confirmation_view",["dashboard_url"=>route($dashboard_route."view",$machine)])
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    @include("warehouse.production_warehouse.dashboard._script")
    <script>
        $('#form1').validate({
            rules: {
                "trans_kind_id_auto": "required",
            }
        });
    </script>
@endsection
