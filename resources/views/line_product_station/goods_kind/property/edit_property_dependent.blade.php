@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>ویرایش حالت های فعال مشخصه {{$goods_kind_property->caption}}
                    </h5>
                </div>
                <form id="form1"
                      action="{{route("line_product_station.goods_kind.property.update_property_dependent",[$goods_kind_property])}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf
                    <div class="card-block">

                        <div class="alert alert-info">
                            در هنگام تکمیل تب مشخصه ها در کالا، هرگاه مشخصه
                            <b>{{$goods_kind_property->parent->caption}}</b>
                            در هر یک از حالت های زیر قرار داشته باشد، مشخصه
                            <b>{{$goods_kind_property->caption}}</b>
                            فعال و به صورت الزامی باید تکمیل گردد.
                        </div>

                        <br/>

                        @if($goods_kind_property->parent->field_type_id == 1 )
                            {{--                            number--}}
                            @for($i=1; $i<=$k; $i++)
                                <div class="row">
                                    <div class="col-md-3">
                                        <br/>
                                        <div class="form-group bold" style="margin-right: 20px">
                                            {{" مقدار عددی ".$goods_kind_property->parent->caption}}
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        @include("component.input._aotocomplet2",[
                                                                            "id"=>"compare_$i",
                                                                            "label"=>"نوع مقایسه",
                                                                            "option"=>$compare_option[$i]["items"],
                                                                            "val"=>$compare_option[$i]["value"],
                                                                            "text"=>$compare_option[$i]["value"],
                                                                            "class_col"=>""
                                                                            ])
                                    </div>
                                    <div class="col-md-4">
                                        @include("component.input._number",["id"=>"value_number_$i","value"=>$values[$i-1]??"","label"=>"<br/>"])

                                    </div>
                                    @if($i!=$k)
                                        <div class="col-md-7"
                                             style="text-align: center; font-size: 18; font-weight: bold; margin: 3px">
                                            And
                                        </div>
                                    @endif

                                </div>
                            @endfor

                        @elseif($goods_kind_property->parent->field_type_id == 3  )
                            {{--                            Select--}}
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>
                                        <input type="checkbox" id="select_all_menu">
                                    </th>
                                    <th> مقدار مشخصه</th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($goods_kind_property->parent->option as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <th>
                                            <input type="checkbox" name="data[value_select][{{$item->id}}]"
                                                   @if(isset($values[$item->id])) checked @endif
                                                   class="myCheckBox_menu">
                                        </th>
                                        <td>
                                            {{$item->caption}}

                                        </td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>

                        @elseif($goods_kind_property->parent->field_type_id == 5 )
                            {{--                            T/F--}}
                            <table class="table table-styling">
                                <tbody>
                                <tr>
                                    <td>
                                        <input type="radio" name="value_check" value="1"
                                               @if(isset($values[1])) checked @endif
                                        > True/انتخاب شده
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="radio" name="value_check" value="-1"
                                               @if(isset($values[0])) checked @endif
                                        > False/انتخاب نشده
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        @endif

                        <a class="btn btn-dark"
                           href="{{route("line_product_station.goods_kind.property.index",$goods_kind_property->goods_kind_id)}}">
                            بازگشت </a>
                        <button type="submit" class="btn btn-success">ذخیره</button>

                    </div>
                </form>

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
                "value_number_1": "required",
            }
        });
        $("#select_all_menu").change(function () {

            $(".myCheckBox_menu").prop('checked', $("#select_all_menu").is(':checked'));
        })
    </script>
@endsection
