@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">
            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>داشبورد برنامه ریزی کانال تولید


                    </h5>
                </div>
                <div class="card-block">

                    <div class="table-responsive" style="min-height: 500px">
                        <table class="table table-styling center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th></th>
                                <th>کد</th>
                                <th> عنوان کانال تولید</th>
                                <th>

                                    <div class="dropdown drp-user show">


                                        <a href="#" class="dropdown-toggle text-success" data-toggle="dropdown"
                                           aria-expanded="true">
                                            دسته بندی کانال
                                            <i class="fa fa-filter"></i>

                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right profile-notification "
                                             style="font-size: 13px;padding: 5px">
                                            <form action="{{route($route)}}" method="post">
                                                @csrf

                                                برابر با:
                                                <select name="production_channel_category_id"
                                                        style="width: 95px; height: 20px">
                                                    @foreach($production_channel_category_option["items"] as $option)
                                                        @if($option["value"] > 0)
                                                            <option value="{{$option["value"]}}" {{isset($option["selected"])?"selected":""}}>{{$option["text"]}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                {{--                    <input type="text"  value="{{$production_status_equal_to}}">--}}
                                                <br/>
                                                <button type="submit" class="btn btn-primary btn-sm btn_search">جستجو
                                                </button>
                                            </form>

                                        </div>
                                    </div>


                                </th>
                                <th>
                                    مقدار کل سفارشات
                                </th>

                                <th>
                                    مقدار باقی مانده سفارشات
                                </th>
                                <th>موجودی کالا</th>
                                <th>موجودی در راه</th>
                                <th>
                                    مقدار لازم جهت سفارشات جاری
                                </th>
                            </tr>


                            </thead>
                            <tbody>
                            @php $row=0;@endphp
                            @foreach($list as $item)
                                <tr style="{{$item->active_status_id == 1210? "background: #1e3953":""}}">
                                    <td>{{++$row}}</td>
                                    <td>
                                        @include("component.input._color_label",["color"=>$item->color])
                                    </td>
                                    <td>
                                        {{$item->id}}
                                    </td>
                                    <td>
                                        {{$item->caption}}
                                    </td>
                                    <td>
                                        {{$item->production_channel_category->caption??""}}
                                    </td>

                                    <td>
                                        @php $all_order_amount=isset($production_channel_type_values[$item->id])?$production_channel_type_values[$item->id]["all_order_amount"]:0 @endphp
{{$all_order_amount}}
                                    </td>


                                    <td>
                                        {{isset($production_channel_type_values[$item->id])?$production_channel_type_values[$item->id]["remaining_order_amount"]:""}}
                                    </td>

                                    <td>
                                        @php $inventory=isset($production_channel_type_values[$item->id])?$production_channel_type_values[$item->id]["current_inventory"]:0;@endphp
                                        {{$inventory}}
                                        <br/>

                                        @include("component.progress._progress_type2",["value"=>round( $inventory / $max_inventory*100),"height"=>17,"background"=>"#fff","class"=>""])

                                    </td>
                                    <td>
                                        @php $in_the_way_amount=isset($production_channel_type_values[$item->id])?$production_channel_type_values[$item->id]["in_the_way_amount"]:0;@endphp
                                        {{$in_the_way_amount}}
                                        <br/>

                                        @include("component.progress._progress_type2",["value"=>round( ($in_the_way_amount) / $max_in_the_way_amount*100),"height"=>17,"background"=>"#fff","class"=>"progress-c-theme2"])


                                    </td>
                                    <td>
                                        @php $p=isset($production_channel_type_values[$item->id])?$production_channel_type_values[$item->id]["current_order_needed_amount"]:0 @endphp


                                        {{ $p}}
                                        <br/>

                                        @include("component.progress._progress_type2",["value"=>round( $p / $max_category*100),"height"=>17,"background"=>"#fff","class"=>"progress-c-theme"])
                                    </td>


                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$list->firstItem()}}</b>
                        تا
                        <b>{{$list->lastItem()}}</b>
                        از
                        <b>{{$list->total()}}</b>
                        رکورد موجود
                    </div>
                </div>
                <div class="text-center">
                    {{$list->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        .btn_search {
            padding: 3px;
            margin-top: 10px;
            float: left;
            margin-left: 17px;
            font-size: 10px;
        }

        select {

            margin-top: 10px;
            margin-left: 17px;
            font-size: 10px;
        }

        .dropdown-toggle::after {
            border: none;
        }

        .progress {
            height: 6px;
            background: #c5bcbc;
        }
        /*.progress-bar.progress-c-theme2 {*/
        /*    background: linear-gradient(-135deg, #899FD4 0%, #A389D4 100%)*/
        /*}*/

    </style>
@endsection
