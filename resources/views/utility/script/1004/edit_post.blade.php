@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <form id="form1" action="{{route("utility.script.1004.update_post",[$script, $post])}}"
          method="post"
          novalidate="novalidate">
        @csrf
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5> ویرایش  {{$script->code." - ".$script->caption}} - پست {{$post->caption}} </h5>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5> لیست رسته های کالایی </h5>
                            </div>
                            <div class="card-block">

                                <table class="table table-bordered center">

                                    <tr>
                                        <th></th>
                                        <th>عنوان رسته کالایی</th>
                                    </tr>

                                    @foreach($goods_kinds as $item)
                                        <tr>
                                            <td>
                                                <input type="checkbox" --}}
                                                       name="data[goods_kind][{{$item->id}}]"
                                                    {{isset($data["post"][$post->id]["goods_kind"][$item->id])?"checked":"" }}
                                                >
                                            </td>
                                            <td>{{$item->caption}}</td>

                                        </tr>
                                    @endforeach

                                </table>
                                <a href="{{route("utility.script.1004.edit",$script)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection

@section("styles")

    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
    <style>
        .tbl_packing_form_status {
            padding: 0;
            margin: 0;
        }

        .tbl_packing_form_status td {
            border: none;
            text-align: right;
        }

    </style>
@endsection

@section("scripts")
    <script>
        $('#form1').validate({});
    </script>
@endsection
