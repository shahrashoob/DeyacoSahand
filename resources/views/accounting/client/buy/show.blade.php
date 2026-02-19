@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مالی ")

@section('content')
    <div style="margin: auto; max-width: 500px">

            <div class="card">
                <div class="card-header">
                    <h5> شارژ حساب </h5>
                </div>
                <div class="card-block"  >

                    <form id="form1" action="{{route("accounting.client.buy.confirm")}}" method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._hidden",["id"=>"amount","value"=>$amount])

                            <br/>
                            <div class="col-md-12">
                                <div class="table-responsive center">
                                    <table class="table table-styling">
                                        <thead>
                                        <tr>
                                            <th>شرح خدمات</th>
                                            <th>قیمت</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>شارژ حساب</td>
                                            <td> {{number_format($amount)}} ریال </td>
                                        </tr>
                                        <tr style="font-weight: bold">
                                            <td>مبلغ کل</td>
                                            <td> {{number_format($amount)}} ریال </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <button type="submit" class="btn btn-lg btn-primary">پرداخت</button>
                            </div>

                        </div>





                        </div>
                    </form>

                </div>
            </div>


    </div>

@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                amount: {required: true, number: true, min: 100, max: 1000000}
            }
        });
    </script>
@endsection
