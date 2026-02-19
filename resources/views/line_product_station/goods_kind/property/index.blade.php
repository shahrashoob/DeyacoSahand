@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>مشخصه های خاص
                        <b>{{$goods_kind->caption}}</b>
                        <a href="{{route("line_product_station.goods_kind.property.create",$goods_kind)}}"
                           class="btn btn-success btn-sm"> مشخصه جدید</a>
                        <a href="{{route("utility.special_unit.create",$goods_kind)}}" class="btn btn-primary btn-sm">
                            واحد خاص جدید</a>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive ">
                        <table class="table table-styling  center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>عنوان مشخصه</th>
                                <th>مشخصه وابسته</th>
                                <th> نوع فیلد</th>
                                <th>واحد اندازه گیری</th>
                                <th> اولویت نمایش</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($goods_kind->property as $item)
                                <tr style="{{$item->status_id==1210?"background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td >
                                        <a class="text-primary"
                                           href="{{route("line_product_station.goods_kind.property.edit",[$goods_kind->id,$item->id])}}">
                                            {{$item->caption}}
                                        </a>

                                    </td>
                                    <td>
                                        @if($item->parent)
                                            <a href="{{route("line_product_station.goods_kind.property.edit_property_dependent",$item)}}">
                                                {{$item->parent->caption ??"" }}<i class="fa fa-edit"></i>
                                            </a>
                                            (<a href="#ds4" type="button" class="  "
                                               style="margin-left:0;" data-toggle="popover"
                                               data-html="true" data-placement="top" title=""
                                               data-content="{{$item->dependent_values_html()}}"
                                               data-original-title="وضعیت های فعال {{$item->caption}}">
                                                {{$item->dependent_values->count()}} حالت
                                            </a>)
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->field_type_id==3)
                                            @php $i=0; $html="";
                                               foreach($item->option as $item_option)
                                                    $html.=++$i."- ".$item_option->caption."<br/>";
                                            @endphp

                                            <a href="#dsf" type="button" class="  "
                                               style="margin-left:0;" data-toggle="popover"
                                               data-html="true" data-placement="top" title=""
                                               data-content="{{$html}}"
                                               data-original-title="{{$item->caption}}">
                                                {{$i}} گزینه انتخاب
                                            </a>

                                            <a class="text-primary"
                                               href="{{route("line_product_station.goods_kind.option.index",$item->id)}}"><i
                                                    class="fa fa-edit"></i> </a>
                                        @elseif($item->field_type_id==1)
                                            <a class="text-primary"
                                               href="{{route("line_product_station.goods_kind.option.edit_number",$item->id)}}">
                                                {{$item->min_value." - ".$item->max_value}}
                                            </a>

                                        @else
                                            {{$item->field_type->caption }}
                                        @endif
                                    </td>
                                    <td>
                                        {{$item->special_unit->caption??"---"}}

                                    </td>
                                    <td>
                                        {{$item->priority_number}}

                                    </td>
                                    <td>
                                        {{$item->status->caption}}

                                    </td>
                                    <td>

                                        <a class="text-danger remove"
                                           href="{{route("line_product_station.goods_kind.property.destroy",[$goods_kind->id,$item->id])}}"><i
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
                    <h5>مشخصه های خاص  لات در رسته
                        <b>{{$goods_kind->caption}}</b>
                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive ">
                        <table class="table table-styling  center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>عنوان مشخصه</th>
                                <th> نوع فیلد</th>
                                <th>واحد اندازه گیری</th>
                                <th> اولویت نمایش</th>
                                <th>وضعیت</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($goods_kind_lot_number_property as $item)
                                <tr style="{{$item->status_id==1210?"background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td >
                                            {{$item->lot_number_property->caption}}
                                    </td>
                                    <td>
                                        @if($item->lot_number_property->field_type_id==3)
                                            @php $i=0; $html="";
                                               foreach($item->lot_number_property->option as $item_option)
                                                    $html.=++$i."- ".$item_option->caption."<br/>";
                                            @endphp

                                            <a href="#" type="button" class="  "
                                               style="margin-left:0;" data-toggle="popover"
                                               data-html="true" data-placement="top" title=""
                                               data-content="{{$html}}"
                                               data-original-title="{{$item->caption}}">
                                                {{$i}} گزینه انتخاب
                                            </a>

                                            <a class="text-primary"
                                               href="#"><i
                                                    class="fa fa-edit"></i> </a>
                                        @elseif($item->lot_number_property->field_type_id==1)
                                            <a class="text-primary"
                                               href="#}">
                                                {{$item->lot_number_property->min_value." - ".$item->lot_number_property->max_value}}
                                            </a>

                                        @else
                                            {{$item->lot_number_property->field_type->caption }}
                                        @endif
                                    </td>
                                    <td>
                                        {{$item->lot_number_property->special_unit->caption??"---"}}

                                    </td>
                                    <td>
                                        {{$item->lot_number_property->priority_number}}

                                    </td>
                                    <td>
                                        {{$item->lot_number_property->status->caption}}

                                    </td>
                                    <td>

                                    </td>


                                </tr>



                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>


            </div>

        </div>

        <div class="col-md-12">
            <a class="btn btn-dark" href="{{route("line_product_station.goods_kind.index")}}"> بازگشت </a>

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        select {
            width: 150px;
        }
        .popover-header,.popover-body{
            font-family: IRANSans !important;
        }
    </style>
@endsection


@section("scripts")
    <script>
        $('body').on('click', function (e) {
            //did not click a popover toggle or popover
            if ($(e.target).data('toggle') !== 'popover'
                && $(e.target).parents('.popover.in').length === 0) {
                $('[data-toggle="popover"]').popover('hide');
            }
        });
        $('#form2').validate({
            rules: {
                "code": "required",
            }
        });
        $(".remove").click(function () {
            return confirm("آیا از حذف این مشخصه اطمینان دارید؟");
        });
    </script>
@endsection

