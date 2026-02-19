@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">

        <div class="col-sm-12">

                <div class="card">
                    <div class="card-header">
                        <h5>ویژگی های خاص ماشین-محصول در ایستگاه
                            <b>{{$station->caption}}</b>
                        </h5>
                    </div>
                    <div class="card-block">

                        <div class="table-responsive">
                            <table class="table table-styling">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>عنوان مشخصه</th>
                                    <th> نوع فیلد</th>
                                    <th>واحد</th>
                                    <th>حداقل</th>
                                    <th>حداکثر</th>
                                    <td></td>
                                </tr>

                                </thead>
                                <tbody>
                                @php $row=0;@endphp
                                @foreach($station->machine_product_property as $item)
                                    <tr>
                                        <td>{{++$row}}</td>
                                        <td>
                                            {{$item->caption}}
                                        </td>

                                        <td>
                                            {{$item->field_type->caption}}
                                        </td>
                                        <td>
                                            {{$item->special_unit->caption??"---"}}
                                        </td>
                                        <td>
                                            {{$item->min_value}}
                                        </td>
                                        <td>
                                            {{$item->max_value}}
                                        </td>


                                    </tr>

                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>


                </div>

            </form>
        </div>

        <div class="col-md-12">
            <a class="btn btn-dark" href="{{route("line_product_station.station.index",$station->line)}}"> بازگشت </a>

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
    </style>
@endsection


@section("scripts")
    <script>
        $('#form2').validate({
            rules: {
                "code": "required",
            }
        });
    </script>
@endsection

