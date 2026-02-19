@extends('oil_change.public._layout')

@section("content")
    <form id="form1" method="post" action="{{route('oil_change.public.store_current_km',[$car,$key])}}"
          autocomplete="false">
        @csrf
        <div class="card">
            <div class="row no-gutters">
                <div class="col-md-12">
                    <h4 style="text-align: center; font-weight: bold;
                        margin-top: 50px;
                        margin-bottom: 10px; " class="mb-4">
                        راننده عزیر برای مشاهده وضعیت سرویس کیلومتر فعلی خودرو را در فرم زیر وارد
                        نمایید</h4>
                </div>
                <div class="col-md-12 col-lg-12">
                    <div class="card-body text-center">
                        <div class="row justify-content-center">
                            <div class="col-sm-12">

                                <table style="margin: auto ">
                                    <tr>
                                        <td colspan="5">
                                            @include("oil_change.home._plack")
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" style="border: none">
                                            <br/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">
                                            @include("component.input._number",["id"=>"current_km",'label'=>"شماره کیلومتر فعلی ","class_col"=>"col-md-12","autofocus"=>1])

                                        </td>
                                    </tr>

                                </table>

                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-lg">مشاهده وضعیت سرویس خودرو</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>
        </div>
    </form>
@endsection

@section("scripts")
    <script type="text/javascript">
        $('#form1').validate({
            rules: {

                current_km: {
                    required: true
                },

            }
        });
    </script>
@endsection
@section("styles")
    <link rel="stylesheet" href="{{asset('oil_change/css/pluck.css')}}">
    @endsection
