@extends('layouts.admin._master',["keypress_enable"=>1])

@section('page_header_title',"داشبورد  تحویل انبار  ")

@php $show_packing_code=!($form->status_id ==500000410 || $form->status_id==500000420); @endphp
@section('content')

    <div class="row">
        <div class="col-sm-12" id="card">
            <div class="card">
                <div class="card-header">
                    <h5>
                        چاپ تجمیعی بسته بندی ها

                    </h5>
                </div>

                <div class="card-block">
                    <form id="form1"
                          action="{{route("wh.dashboard.submit_print_packing_form",[$form,$page])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="alert alert-warning col-md-12">
                                برای چاپ بسته بندی ها به صورت تجمیعی می توانید از ردیف 1 تا ردیف {{$max}} را به صورت
                                دلخواه چاپ نمایید.
                                <br/>
                                تعداد چاپ در هر مرحله حداکثر 50 بسته بندی می باشد.
                            </div>
                            @include("component.input._number",["id"=>"from_row",'label'=>"از ردیف ","value"=>""])
                            @include("component.input._number",["id"=>"to_row",'label'=>"تا ردیف ","value"=>""])


                        </div>


                        <div style="text-align: center">

                            <a href="{{route("wh.dashboard.show_form",$form)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary">تایید و چاپ</button>

                        </div>
                    </form>
                </div>


            </div>


        </div>


    </div>
    <div class="row">
        <div class="col-sm-12" id="card">
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست بسته بندی ها جهت چاپ برچسب

                    </h5>
                </div>

                <div class="card-block">
                    <form id="form1"
                          action="{{route("wh.dashboard.submit_print_packing_form",[$form,$page])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-styling center">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>
                                            <input type="checkbox" id="select_all">
                                        </th>
                                        <th> کد بسته بندی</th>
                                        <th>نوع بسته بندی</th>
                                        <th>مقدار کل</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=$packing_list->firstItem();@endphp
                                    @foreach($packing_list as $item)
                                        <tr>
                                            <td>{{$row++}}</td>
                                            <td>

                                                <input class="myCheckBox"
                                                       name="data[packing_form][{{$item->id}}]"
                                                       id="packing_{{$item->id}}"
                                                       data-id="{{$item->id}}"
                                                       type="checkbox" checked>

                                            </td>
                                            <td>{{$show_packing_code?$item->getCode():"***"}}</td>
                                            <td>{{$item->packing_type->caption??""}}</td>
                                            <td>{{$item->getFinalAmount()}}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>

                            </div>
                            <div class="float-left">
                                نمايش رکوردهای
                                <b>{{$packing_list->firstItem()}}</b>
                                تا
                                <b>{{$packing_list->lastItem()}}</b>
                                از
                                <b>{{$packing_list->total()}}</b>
                                رکورد موجود


                            </div>
                        </div>

                        <div class="text-center">
                            {{$packing_list->links('pagination::bootstrap-4')}}
                        </div>
                        <div style="text-align: center">

                            <a href="{{route("wh.dashboard.show_form",$form)}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary">انتخاب و چاپ</button>

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
                "from_row": {required: true, min: 1, max: {{$max}} },
                "to_row": {required: true, min: 1, max: {{$max}} },
            }
        });

    </script>
@endsection


