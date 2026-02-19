@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>
                        افزودن حامل جدید از منظومه داده ای
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.carrier.carrier_type_ic.search")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"search",'label'=>"متن جستجو ","value"=>$search])

                        </div>

                        <a href="{{route("line_product_station.carrier.carrier_type.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> جستجو</button>

                    </form>
                    @if ($search!="")
                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th> کد نوع حامل</th>
                                    <th> عنوان</th>
                                    <th>گروه حامل</th>
                                    <th>واحد سنجش <br/> کالای حامل</th>
                                    <th>حداقل باند</th>
                                    <th>حداکثر باند</th>
                                    <th>حداقل ظرفیت <br/>هر باند</th>
                                    <th>حداکثر ظرفیت <br/>هر باند</th>
                                    <th> در انبار <br/> قرار می گیرد؟</th>
                                    <th> قابلیت شماره <br/>گذاری دارد؟</th>
                                    <th>سیستم می تواند <br/>حامل جدید تعریف کند</th>
                                    <th>وزن</th>
                                    <th>افزودن</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>
                                            {{$item["id"]}}
                                        </td>
                                        <td>
                                            {{$item["caption"]}}
                                        </td>
                                        <td>{{$item["carrier_group"]['caption']}}</td>
                                        <td>{{ $unit->caption ?? ""}}</td>
                                        <td>{{$item["min_band_number"]}}</td>
                                        <td>{{$item["max_band_number"]}}</td>
                                        <td>{{$item["min_band_capacity"]}}</td>
                                        <td>{{$item["max_band_capacity"]}}</td>
                                        <td>{!! $item["placed_in_warehouse"]?"<i class='fa fa-check'></i>" :""!!}</td>
                                        <td>{!! $item["has_number_ability"]?"<i class='fa fa-check'></i>":"" !!}</td>
                                        <td>{!! $item["system_can_define_new_carrier"]?"<i class='fa fa-check'></i>":"" !!}</td>
                                        <td>{{$item["average_weight"]}}</td>
                                        <td>
                                            <a class="text-success" href="{{route("line_product_station.carrier.carrier_type_ic.create",$item["id"])}}">
                                                <i class="fa fa-plus-circle "></i> افزودن
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </div>

@endsection
@section("styles")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
{{--@section("scripts")--}}
{{--    <script>--}}
{{--        $('#form1').validate({--}}
{{--            rules: {--}}
{{--                "search": "required",--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}
{{--@endsection--}}