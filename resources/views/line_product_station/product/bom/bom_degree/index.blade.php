@extends('layouts.admin._master')
@section('page_header_title',$product_creation_process?"داشبورد طراحی کالا":" کارتابل مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست درجه های مجاز
                        {{$material->fullCaption()}}

                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive">
                        <table class="table table-styling">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد درجه</th>
                                <th> عنوان درجه</th>
                                <td></td>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        {{$item->degree->code??""}}
                                    </td>
                                    <td>
                                        {{$item->degree->caption??""}}
                                    </td>

                                    <td>
                                        <a class="text-danger"
                                           href="{{route("line_product_station.product.bom_degree.delete",[$item->id,$item->product_id,$item->material_id,$item->degree_id,$item])}}"
                                           onclick="return confirm('آیا از حذف اطمینان دارید؟')"
                                        >
                                            <i
                                                class="fa fa-trash"></i> </a>
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن درجه جدید
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.product.bom_degree.store",[$product,$material,$bom_item,$product_creation_process])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="col-md-6">
                        @include("component.input._checkbox",["id"=>"all_degree_ids","label"=>"همه درجه های کالا انتخاب شوند","checked"=>false])
                        </div>
                        <div class="col-md-6">
                            @include("component.input._aotocomplet2",[
                                "id"=>"degree_id",
                                "label"=>" درجه کالا ",
                                "option"=>$degree_option["items"],
                                "val"=>"",
                                "text"=>"",
                                "class_col"=>""
                                ])

                            @include("component.input._hidden",["id"=>"back_to_bom","value"=>0])
                        </div>
                        @if($product->supply_type_id == 1)
                            @include("component.input._number",["id"=>"input_line_code_from",'label'=>"  از خط ورودی","value"=>$input_line_code_from??$bom_item->input_line_code])
                            @include("component.input._number",["id"=>"input_line_code_to",'label'=>"تا خط ورودی","value"=>$input_line_code_to??$bom_item->input_line_code])
                        @endif
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success" id="btn_back1"><i
                                    class="fa fa-plus"></i> افزودن
                            </button>

                            <button type="submit" class="btn btn-success" id="btn_back2"><i
                                    class="fa fa-plus"></i> افزودن و بازگشت
                            </button>
                            @if($product_creation_process)
                                <a href="{{route("line_product_station.product.product_creation.bom.index",$product_creation_process)}}"
                                   class="btn btn-outline-dark">بازگشت</a>
                            @else
                                <a href="{{route("line_product_station.product.bom.index",$product)}}"
                                   class="btn btn-outline-dark">بازگشت</a>
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
        $("#btn_back1").click(function () {
            $("#back_to_bom").val(1);
        })
        $("#btn_back2").click(function () {

            $("#back_to_bom").val(2);
        })
        $("#switch-all_degree_ids").change(function (){
            if($("#switch-all_degree_ids").is(":checked")){

                $("#degree_id_auto").parent().parent().parent().css("display","none")
            }
            else{
                $("#degree_id_auto").parent().parent().parent().css("display","")
            }
        })
    </script>
@endsection
