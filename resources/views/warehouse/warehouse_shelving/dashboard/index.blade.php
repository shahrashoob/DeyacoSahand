@extends('layouts.admin._master',["keypress_enable"=>1])
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>مشخصات کالاهای مجاز قفسه
                        {{$warehouseShelving->fullCode()}}
                        در
                        {{$warehouseShelving->warehouse->caption}}

                    </h5>
                </div>
                <div class="card-block">

                    @if(count($warehouse_shelving_product_list))
                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد کالای مجاز</th>
                                <th> نام کالای مجاز</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($warehouse_shelving_product_list as $item)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->product->code}}

                                    </td>
                                    <td>
                                        {{$item->product->caption}}

                                    </td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$warehouse_shelving_product_list->firstItem()}}</b>
                        تا
                        <b>{{$warehouse_shelving_product_list->lastItem()}}</b>
                        از
                        <b>{{$warehouse_shelving_product_list->total()}}</b>
                        رکورد موجود
                    </div>
                    @else
                        <div class="alert alert-warning">هیچ کالایی مجاز به قرار گرفتن در این محل نمی باشد.</div>
                    @endif
                </div>
                <div class="text-center">
                    {{$warehouse_shelving_product_list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>


        <div class="col-sm-12">
            {{--            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])--}}
            <div class="card">
                <div class="card-header">
                    <h5>مشخصات بسته بندی های موجود در قفسه
                        {{$warehouseShelving->fullCode()}}


                    </h5>
                </div>
                <div class="card-block">
                    @if(count($packing_forms))
                    <div class="table-responsive">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد بسته بندی</th>
                                <th>مقدار</th>
                                <th>وزن ناخالص</th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($packing_forms as $packing_form)
                                <tr>
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$packing_form->code}}

                                    </td>
                                    <td>
                                        {{$packing_form->getAmount()}}

                                    </td>
                                    <td>
                                        {{$packing_form->gross_weight}}
                                    </td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$warehouse_shelving_product_list->firstItem()}}</b>
                        تا
                        <b>{{$warehouse_shelving_product_list->lastItem()}}</b>
                        از
                        <b>{{$warehouse_shelving_product_list->total()}}</b>
                        رکورد موجود
                    </div>
                    @else
                        <div class="alert alert-warning">هیچ بسته بندی در این محل قرار ندارد.</div>
                    @endif
                </div>
                <div class="text-center">
                    {{$warehouse_shelving_product_list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

{{--        @if(count($reservoirs))--}}
{{--            <div class="col-sm-12">--}}
{{--                --}}{{--            @include("utility.public._search_view",["route"=>"accounting.definition.cost_center.index"])--}}
{{--                <div class="card">--}}
{{--                    <div class="card-header">--}}
{{--                        <h5>مشخصات مخزن های موجود در قفسه--}}
{{--                            {{$warehouseShelving->fullCode()}}--}}
{{--                        </h5>--}}
{{--                    </div>--}}
{{--                    <div class="card-block">--}}

{{--                        <div class="table-responsive">--}}
{{--                            <table class="table table-styling center">--}}
{{--                                <thead>--}}
{{--                                <tr>--}}
{{--                                    <th>#</th>--}}
{{--                                    <th>کد مخزن</th>--}}
{{--                                    <th>نام مخزن</th>--}}
{{--                                    <th>موجودی</th>--}}
{{--                                </tr>--}}

{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                @php $row=0;@endphp--}}
{{--                                @foreach($reservoirs as $reservoir)--}}
{{--                                    <tr>--}}
{{--                                        <td>{{++$row}}</td>--}}
{{--                                        <td>--}}
{{--                                            {{$reservoir->packing_form->code}}--}}

{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            {{$reservoir->caption}}--}}

{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            {{$reservoir->getAmount()}} {{$reservoir->unit->caption}}--}}

{{--                                        </td>--}}



{{--                                    </tr>--}}
{{--                                @endforeach--}}
{{--                                </tbody>--}}

{{--                            </table>--}}
{{--                        </div>--}}
{{--                        <div class="float-left">--}}
{{--                            نمايش رکوردهای--}}
{{--                            <b>{{$warehouse_shelving_product_list->firstItem()}}</b>--}}
{{--                            تا--}}
{{--                            <b>{{$warehouse_shelving_product_list->lastItem()}}</b>--}}
{{--                            از--}}
{{--                            <b>{{$warehouse_shelving_product_list->total()}}</b>--}}
{{--                            رکورد موجود--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="text-center">--}}
{{--                        {{$warehouse_shelving_product_list->links('pagination::bootstrap-4')}}--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}

        @if($add_add_packing)
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h5> افزودن بسته بندی به قفسه </h5>
                    </div>
                    <div class="card-block">

                        <form id="form1"
                              action="{{route("wh.warehouse_shelving.dashboard.submit_add_packing_form",$warehouseShelving)}}"
                              method="post"
                              autocomplete="off"
                              novalidate="novalidate">
                            @csrf
                            <div class="row">

                                @if($warehouseShelving->warehouse->allow_entry_with_pin)

                                    @include("component.input._text",["id"=>"packing_form_pin",'label'=>"کد Pin بسته بندی","value"=>"","autofocus"=>"1"])
                                @else

                                    @include("component.input._number",["id"=>"packing_form_code",'label'=>"کد بسته بندی","value"=>"","autofocus"=>"1"])

                                @endif


                                <div class="w-100"><br/></div>

                            </div>



                            <button type="submit" class="btn btn-primary"> ثبت</button>

                        </form>

                    </div>
                </div>
            </div>
{{--            <div class="col-sm-6">--}}
{{--                <div class="card">--}}
{{--                    <div class="card-header">--}}
{{--                        <h5> افزودن مخزن به قفسه </h5>--}}
{{--                    </div>--}}
{{--                    <div class="card-block">--}}

{{--                        <form id="form1"--}}
{{--                              action="{{route("wh.warehouse_shelving.dashboard.submit_add_reservoir",$warehouseShelving)}}"--}}
{{--                              method="post"--}}
{{--                              autocomplete="off"--}}
{{--                              novalidate="novalidate">--}}
{{--                            @csrf--}}
{{--                            <div class="row">--}}

{{--                                @include("component.input._number",["id"=>"reservoir_code",'label'=>"کد مخزن","value"=>""])--}}

{{--                                <div class="w-100"><br/></div>--}}

{{--                            </div>--}}



{{--                            <button type="submit" class="btn btn-primary"> ثبت</button>--}}

{{--                        </form>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        @endif


{{--            <a href="{{route("wh.warehouse_shelving.definition.index",$warehouseShelving->warehouse)}}"--}}
{{--               class="btn btn-outline-dark">بازگشت</a>--}}

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
