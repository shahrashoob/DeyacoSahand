@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

            {{-- @include("orders._search_view",["route"=>"wh.material.list"]) --}}
            <div class="card">
                <div class="card-header">
                    <h5>لیست  دسته عملیات ها  برای
                        <b>
                            {{ $station->caption}}
                        </b>

                    </h5>
                </div>
                <div class="card-block">
                    <form id="form1"
                          action="{{route("line_product_station.station.station_category.store",[$station])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th></th>
                                    <th>دسته عملیات</th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($operation_categories as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            <input type="checkbox" name="categories[{{$item->id}}]"
                                                   @if(isset($station_categories[$item->id])) checked='checked' @endif>

                                        </td>
                                        <td>
                                            {{$item->caption}}

                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>


                        <button type="submit" class="btn btn-primary"> ذخیره و بازگشت</button>
                        <a href="{{route("line_product_station.station.index",$station->line_id)}}"
                           class="btn btn-outline-dark">بازگشت</a>
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
                "x": "required",
            }
        });
    </script>
@endsection
