@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>
                        افزودن بسته بندی  جدید از منظومه داده ای
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.packing.packing_type_ic.search")}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            @include("component.input._text",["id"=>"search",'label'=>"متن جستجو ","value"=>$search])

                        </div>

                        <a href="{{route("line_product_station.packing.packing_type.index")}}"
                           class="btn btn-outline-dark">بازگشت</a>

                        <button type="submit" class="btn btn-primary"> جستجو</button>

                    </form>
                    <br/>
                    @if ($search!="")
                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th>کد</th>
                                    <th> عنوان</th>
                                    <th>وضعیت</th>
                                    <th>افزودن</th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{$row++}}</td>
                                        <td>{{$item["id"]}}</td>

                                        <td>
                                            <a  href="{{route("line_product_station.packing.packing_type_ic.show",$item["id"])}}"
                                               >
                                                {{$item["caption"]}}
                                            </a>


                                        </td>

                                        <td>{{$active_status->caption}}</td>
                                        <td>
                                            <a class="text-success" href="{{route("line_product_station.packing.packing_type_ic.create",$item["id"])}}"
                                               onclick="return confirm('آیا از افزودن بسته بندی اطمینان دارید؟')">
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