@extends('layouts.admin._master')

@section('page_header_title',"داشبورد  انبار  ")

@section('content')


    <form id="form1" autocomplete="off" action="{{route("wh.out.exit_form_implementation.confirm")}}"
          method="post"
          novalidate="novalidate">
        @csrf
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
                <div class="card">
                    <div class="card-header">
                        <h5>مشخصات بسته بندی ها</h5>
                    </div>

                    @include("component.input._hidden",["id"=>"packing_form_list_ids","value"=>json_encode($packing_form_list_ids)])
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive center">
                                <table class="table table-styling">
                                    <thead>
                                    <tr>
                                        <th style="width: 10px">ردیف</th>
                                        <th>کد بسته بندی</th>
                                        <th>مقدار</th>
                                        <th></th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=0;@endphp
                                    @foreach($packing_form_list as $packing_form)
                                        <tr>
                                            <td>{{++$row}}</td>
                                            <td>{{$packing_form->code}}</td>
                                            <td>{{$packing_form->getAmount()}}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach


                                    </tbody>
                                </table>
                            </div>


                        </div>

                    </div>

                </div>
            </div>
            <div class="col-md-12 center">
                <button type="submit" class="btn btn-success submit_form" > ثبت  نهایی فرم</button>
            </div>

        </div>
    </form>



@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection


@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "trans_kind_id_auto": "required",
            }
        });
    </script>

@endsection

