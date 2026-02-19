@extends('layouts.admin._master',["keypress_enable"=>1])

@section('page_header_title',"داشبورد  تحویل انبار  ")

@section('content')
    <div class="row">
        <div class="col-sm-12" id="card">
            <div class="card">
                <div class="card-header">
                    <h5>
                        لیست بسته بندی های باز شده در تحویل درخواست
                        {{$product_request_form->getCode()}}
                    </h5>
                </div>

                <div class="card-block">
                    <form id="form1" action="{{route("wh.out.delivery.submit_select_new_packing_type",[$product_request_form,$page])}}"
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
                                        <th> کد بسته بندی</th>
                                        <th>تعداد بسته های انتخاب شده</th>
                                        <th>نوع بسته بندی فرعی</th>
                                        <th>نیاز به بسته بندی مجدد</th>
                                        <th>نوع بسته بندی های مجاز</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    @php $row=1;@endphp
                                    @foreach($selected_packing_list as $item)
                                        @if(isset($count_select[$item->id]))
                                            <tr>
                                                <td>{{$row++}}</td>
                                                <td>{{$item->getCode()}}</td>
                                                <td>{{$count_select[$item->id] }}
                                                    از {{$item->sub_packing_form_number}}</td>
                                                <td>{{$item->packing_type->first_packing_type->fullCaption()}}</td>
                                                <td>
                                                    <input class="need_new_packing_radio" type="radio"
                                                           data-packing_form_id="{{$item->id}}"
                                                           value="1"
                                                           checked
                                                           name="data[need_new_packing][{{$item->id}}]"
                                                           required
                                                    >
                                                    بله
                                                    &nbsp;
                                                    &nbsp;
                                                    &nbsp;

{{--                                                    <input class="need_new_packing_radio" type="radio"--}}
{{--                                                           data-packing_form_id="{{$item->id}}"--}}
{{--                                                           value="-1"--}}
{{--                                                           name="data[need_new_packing][{{$item->id}}]"--}}
{{--                                                           required--}}
{{--                                                    >--}}
{{--                                                    خیر--}}

                                                </td>
                                                <td>
                                                    @include("component.input._select_simple",["id"=>"select_packing_form_".$item->id,"option"=>$packing_type_list_option[$item->id],"class"=>"","style"=>"width:200px","required"=>1])
                                                </td>

                                            </tr>
                                        @endif
                                    @endforeach


                                    </tbody>
                                </table>
                            </div>

                        </div>


                        <div style="text-align: center">

                            <a href="{{route("wh.out.dashboard.view",[$product_request_form->id])}}"
                               class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" id="btn_submit" class="btn btn-primary">ثبت و ادامه</button>

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
                "unit_id": "required",
                "sub_unit_id": "required",
                "carrier_code": "required",
                "lot_number": "required",
                "degree_id_auto": "required",

                @foreach($selected_packing_list as $item)
                @if(isset($count_select[$item->id]))

                @endif
                @endforeach
            }
        });
        $(".need_new_packing_radio").change(function () {
            var packing_form_id=$(this).data("packing_form_id");
            if($(this).val() == 1){
                $("#select_packing_form_"+packing_form_id).prop("disabled",0);
            }else{
                $("#select_packing_form_"+packing_form_id).prop("disabled",1);
            }

        })

        $("#btn_submit").click(function (){

            @foreach($selected_packing_list as $item)
            @if(isset($count_select[$item->id]))
            @endif
            @endforeach
        })
    </script>
@endsection


