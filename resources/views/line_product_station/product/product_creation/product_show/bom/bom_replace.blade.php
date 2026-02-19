@extends('layouts.admin._master',["no_persian"=>1])
@section("page_header_title",$product_creation_process?"داشبورد طراحی کالا":" داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست کالاهای مجاز جایگزین مصرف برای
                        {{$material->fullCaption()}}

                    </h5>
                </div>
                <div class="card-block">

                        <div class="table-responsive center">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>

                                    </th>
                                    <th>کد کالا</th>
                                    <th> عنوان کالا</th>
                                    <th>مقدار</th>
                                    <th>تعداد</th>
                                    <th>درصد استفاده</th>
                                    <th>اولویت انتخاب</th>
                                    <th>تعداد جایگزین تولید </th>
                                    <th>پیش بینی ضایعات</th>
                                    <th>ضریب اصلاح مصرف</th>
                                    <th>پیش بینی ضریب اصلاح مصرف</th>

                                </tr>

                                </thead>
                                <tbody>
                                <tr style="font-weight: bold">
                                    <td></td>
                                    <td>{{$material->code}}</td>
                                    <td>{{$material->caption}}</td>
                                    <td>{{$bom_item->amount}}</td>
                                    <td>{{$bom_item->number}}</td>
                                    <td>{{$bom_item->percent_of_use}}</td>
                                    <td>

                                        {{$bom_item->priority_number_in_replace}}
                                    </td>
                                    <td></td>
                                    <td>{{$bom_item->waste_prediction}}</td>
                                    <td>{{$bom_item->consumption_correction_factor}}</td>
                                    <td>{{$bom_item->consumption_correction_factor_prediction}}</td>
                                    <td></td>
                                </tr>
                                @php $row=0;@endphp
                                @foreach($replace_product_list as $item)
                                    @php $replace_product=$bom_item->replaces()->where("replace_product_id",$item->replace_product_id)->first();@endphp
                                    @if(isset($replace_product))
                                    <tr>
                                        <td>{{++$row}}</td>

                                        <td>
                                            {{$item->replace_product->code??""}}
                                        </td>
                                        <td>
                                            {{$item->replace_product->caption??""}}
                                        </td>
                                        <td>
                                          {{isset($replace_product)?$replace_product->amount:$bom_item->amount}}
                                        </td>
                                        <td>

                                                 {{isset($replace_product)?$replace_product->number:$bom_item->number}}
                                        </td>
                                        <td>

                                                {{isset($replace_product)?$replace_product->percent_of_use:$bom_item->percent_of_use}}
                                        </td>
                                        <td>

                                             {{isset($replace_product)?$replace_product->priority_number:$row+1}}
                                        </td>
                                        <td>
                                            {{isset($replace_product)?$replace_product->product_permutation_count():0}}
                                            عدد

                                        </td>

                                        <td>

                                                   {{isset($replace_product)?$replace_product->waste_prediction:$bom_item->waste_prediction}}
                                        </td>
                                        <td>

                                                   {{isset($replace_product)?$replace_product->consumption_correction_factor:$bom_item->consumption_correction_factor}}

                                        </td>
                                        <td>

                                            {{isset($replace_product)?$replace_product->consumption_correction_factor_prediction:$bom_item->consumption_correction_factor_prediction}}

                                        </td>



                                    </tr>
                                    @endif
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="center">


                                <a class="btn btn-outline-dark"
                                   href="{{route("line_product_station.product.product_creation.product_show.bom.index",$product_creation_process)}}"
                                >بازگشت </a>


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
    <style>
        input {
            width: 100px;
            text-align: center;
        }
    </style>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "degree_id_auto": "required",
            }
        });

        $("")

    </script>
@endsection
