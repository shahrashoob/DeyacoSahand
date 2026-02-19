@extends('layouts.admin._master',["keypress_enable"=>1,"no_persian"=>1])

@section('page_header_title',"داشبورد  انبار  ")

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>مشخصات انبار</h5>
                </div>
                <div class="card-block">


                    <div class="row">


                        @include("component.input._lable",[
                            "label"=>"نوع تراکنش",
                            "value"=>$trans_kind->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"trans_kind_id",
                            "value"=>$trans_kind->id??""
                            ])

                        @include("component.input._lable",[
                            "label"=>"طرف حساب",
                            "value"=>$opp_kind->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"opp_kind_id",
                            "value"=>$opp_kind->id??""
                            ])

                        @include("component.input._lable",[
                            "label"=>"مرکز هزینه",
                            "value"=>$cost_center->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"cost_center_id",
                            "value"=>$cost_center->id??""
                            ])

                        @include("component.input._lable",[
                            "label"=>"انبار",
                            "value"=>$warehouse->caption??""
                            ])
                        @include("component.input._hidden",[
                            "id"=>"warehouse_id",
                            "value"=>$warehouse->id??""
                            ])


                        @include("component.input._lable",[
                            "label"=>"شرح تراکنش انبار",
                            "value"=>$description
                            ])
                        @include("component.input._hidden",[
                            "id"=>"description",
                            "value"=>$description
                            ])


                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>لیست کالا ها</h5>
                        </div>
                        <div class="card-block">

                            <form id="form1" autocomplete="off" action="{{route("wh.out.exit_form_implementation2.confirm_read_product_info_type4")}}"
                                  method="post"
                                  novalidate="novalidate">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive center">
                                            <table class="table table-styling">
                                                <thead>
                                                <tr>
                                                    <th style="width: 10px">ردیف</th>
                                                    <th>کد کالا</th>
                                                    <th>نام  کالا</th>
                                                    <th>مقدار</th>
                                                    <th>لات (همبافت)</th>
                                                    <th>درجه</th>
                                                    <th></th>
                                                </tr>

                                                </thead>
                                                <tbody>
                                                @php $row=0;@endphp
                                                @foreach($product_list as $product)
                                                    <tr>
                                                        <td>{{++$row}}</td>
                                                        <td>{{$product->code}}</td>
                                                        <td>{{$product->caption}}</td>
                                                        <td>{{$product_ids_amount[$product->id]}}  {{$product->unit->caption}}</td>
                                                        <td>{{$product_ids_lot_number_degree[$product->id]["lot_number_code"]}} </td>
                                                        <td>{{$product_ids_lot_number_degree[$product->id]["degree_code"]}} </td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach


                                                </tbody>
                                            </table>
                                        </div>


                                    </div>

                                </div>

                                <div class="col-md-12 center" id="btn_submit">
                                    <a class="btn btn-outline-dark" href="{{route("wh.out.exit_form_implementation2.index")}}">بارگشت</a>
                                    <button type="submit" class="btn btn-primary submit_form"> ثبت و ادامه</button>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>

@endsection
@section("styles")

@endsection


@section("scripts")
    <script>

        $('#form1').validate({
            rules: {
                "caption": "required",
                "product_id_0": "required",
                "final_amount_0": "required",
            }
        });


    </script>
@endsection

