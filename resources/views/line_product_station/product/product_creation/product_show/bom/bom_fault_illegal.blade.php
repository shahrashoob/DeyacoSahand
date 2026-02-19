@extends('layouts.admin._master')
@section('page_header_title',"داشبورد طراحی کالا")
@section("content")
    <div class="row">


        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست نقص های غیر مجاز

                    </h5>
                </div>
                <div class="card-block">

                        <div class="col-md-12">
                            <div class="alert alert-info">
                                لیست نقص های غیر مجاز

                                {{$material->fullCaption()}}

                                برای کالای {{$product->fullCaption()}}

                            </div>
                        </div>
                        <div class="col-md-12">
                            <table class="table" style="text-align: right">
                                @php $row=0;@endphp
                                @foreach($product_fault_list as $product_fault)

                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            {{$product_fault->product_fault->caption}}
                                        </td>
                                    </tr>



                                @endforeach
                            </table>
                        </div>
                        <div class="col-md-12">
                            <br/>

                            <a href="{{route("line_product_station.product.product_creation.product_show.bom.index",$product_creation_process)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                        </div>


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
    <script>
        $('#form1').validate({
            rules: {
                "degree_id_auto": "required",
            }
        });

    </script>
@endsection
