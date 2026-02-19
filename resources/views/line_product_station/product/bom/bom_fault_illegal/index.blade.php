@extends('layouts.admin._master')
@section('page_header_title',$product_creation_process?"داشبورد طراحی کالا":" کارتابل مدیریت ")
@section("content")
    <div class="row">


        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>لیست نقص های غیر مجاز

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.product.bom_fault_illegal.store",[$product,$material,$bom_item,$product_creation_process])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                لیست نقص های غیر مجاز

                                {{$material->fullCaption()}}

                                برای کالای {{$product->fullCaption()}}

                            </div>
                        </div>
                        <div class="col-md-12">
                            <table class="table" style="text-align: right">
                                @foreach($product_fault_list as $product_fault)

                                    <tr>
                                        <td style="width: 30px">
                                            <input type="checkbox"
                                                   name="bom_item_fault_illegal[{{$product_fault->product_fault_id}}]"
                                                {{in_array($product_fault->product_fault_id,$bom_item_fault_illegals)?"checked=checked":""}}
                                            >
                                        </td>
                                        <td>
                                            {{$product_fault->product_fault->caption}}
                                        </td>
                                    </tr>



                                @endforeach
                            </table>
                        </div>
                        <div class="col-md-12">
                            <br/>

                            <button type="submit" class="btn btn-primary">
                                ذخیره
                            </button>
                            @if($product_creation_process)
                                <a class="btn btn-outline-dark" href="{{route("line_product_station.product.product_creation.bom.index",$product_creation_process)}}">بازگشت</a>
                            @else
                                <a class="btn btn-outline-dark" href="{{route("line_product_station.product.bom.index",$product)}}">بازگشت</a>
                            @endif

                        </div>
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
    <script>
        $('#form1').validate({
            rules: {
                "degree_id_auto": "required",
            }
        });

    </script>
@endsection
