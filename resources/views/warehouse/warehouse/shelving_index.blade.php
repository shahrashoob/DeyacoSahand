@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> مدیریت قفسه بندی برای {{$warehouse->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("wh.warehouse.shelving.submit",$warehouse)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>نام ردیف</th>
                                    <th>حداکثر تعداد</th>
                                    <th>نوع کاراکتر (1:عدد،2: حروف)</th>
                                    <th>تعداد بخش در نام گذاری
                                    </th>
                                    <th>وضعیت نمایش در کد سلول</th>
                                </tr>

                                @foreach($shelving_cells_type as $id=>$item)
                                    <tr>
                                        <td>{{$item}}</td>
                                        <td>
                                            <input name="cell[number][{{$id}}]" type="number" max="26" min="1" value="{{$shelving_cells["line".$id."_number"]}}">
                                        </td>
                                        @if($id==5)
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @else

                                            <td>
                                                <input name="cell[type][{{$id}}]" type="number" max="2" min="1" value="{{$shelving_cells["line".$id."_type"]}}">
                                            </td>
                                            <td>
                                                <input name="cell[part][{{$id}}]" type="number" max="5" min="1" value="{{$shelving_cells["line".$id."_part"]}}">
                                            </td>
                                            <td>
                                                <input  name=cell[status][{{$id}}]" type="checkbox"  {{$shelving_cells["line".$id."_status_id"]==1200?"checked='checked'":""}}">
                                            </td>
                                        @endif
                                    </tr>

                                @endforeach


                                <tbody>
                                </tbody>
                            </table>


                            <a href="{{route("wh.warehouse.index")}}" class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> چاپ لیبل قفسه ها  </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("wh.warehouse.shelving.print_label",$warehouse)}}" method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-styling center">
                                <thead>
                                <tr>
                                    <th>نام ردیف</th>
                                    <th> کد ردیف</th>
                                </tr>

                                @foreach($shelving_cells_type as $id=>$item)
                                    @if($id< 5)
                                        <tr>
                                            <td>{{$item}}</td>
                                            <td>
                                                <input name="cell_info[{{$id}}]" type="number" max="26" min="1" value="{{$shelving_cells["line".$id."_number"]}}">
                                            </td>

                                        </tr>
                                    @endif

                                @endforeach
                                <tr>
                                    <th>نوع فلش</th>
                                    <th>
                                    <input type="radio"  name="updown" checked value="up" > فلش رو به بالا
                                    <input type="radio"  name="updown"   value="down" > فلش رو به پایین
                                    </th>
                                </tr>

                                <tbody>
                                </tbody>
                            </table>


                            <a href="{{route("wh.warehouse.index")}}" class="btn btn-outline-dark">بازگشت</a>

                            <button type="submit" class="btn btn-primary"> چاپ لیبل قفسه</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({
            rules: {
                "caption": "required",
                "code": "required",
            }
        });
    </script>
@endsection
