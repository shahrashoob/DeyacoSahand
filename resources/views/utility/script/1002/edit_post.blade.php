@extends('layouts.admin._master')

@section('page_header_title'," کارتابل مدیریت ")

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card" style="overflow: auto">
                <div class="card-header">
                    <h5> ویرایش  {{$script->code." - ".$script->caption}} - پست {{$post->caption}} </h5>
                </div>
                <div class="card-block">

                    <form id="form1" action="{{route("utility.script.1002.update_post",[$script, $post])}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">


                                <table class="table table-bordered center">

                                    <tr>

                                        <th>عنوان رسته کالایی</th>
                                        <th>در صورتی که مقدار بسته بندی در در انتظار تایید انبار را برابر با 1000 قرار داده شود، در صورتی که موجودی کالا از 1000 واحد بیشتر شود، پیامک ارسال می گردد. </th>
                                    </tr>

                                    @foreach($goods_kinds as $item)
                                        <tr>

                                            <td style="vertical-align: middle">{{$item->caption}}</td>
                                            <td>

                                                @php $row=0;@endphp
                                                <table class="tbl_packing_form_status">
                                                    <tr>
                                                        @foreach($packing_form_status_list as $packing_item)
                                                            <td>
                                                                <input type="checkbox"
                                                                       name="data[goods_kind][{{$item->id}}][packing_status][{{$packing_item->id}}]"
                                                                    {{isset($data["post"][$post->id]["goods_kind"][$item->id]["packing_status"][$packing_item->id])?"checked":"" }}
                                                                >
                                                                {{$packing_item->caption}}
                                                            </td>
                                                        <td>

                                                                <input type="number" style="width: 120px"
                                                                       name="data[goods_kind][{{$item->id}}][packing_status][min][{{$packing_item->id}}]"
                                                                    value="{{isset($data["post"][$post->id]["goods_kind"][$item->id]["packing_status"]["min"][$packing_item->id])?$data["post"][$post->id]["goods_kind"][$item->id]["packing_status"]["min"][$packing_item->id]:"" }}"
                                                                >

                                                            </td>
                                                            &nbsp;
                                                            @if($row % 3 == 2)
                                                                </tr>
                                                                <tr>
                                                                @endif
                                                        @php $row++;@endphp
                                                        @endforeach
                                                    </tr>
                                                </table>

                                            </td>
                                        </tr>
                                        @endforeach

                                </table>

                                <br/>
                                <br/>
                                <a href="{{route("utility.script.1002.edit",$script)}}"
                                   class="btn btn-outline-dark">بازگشت</a>

                                <button type="submit" class="btn btn-primary"> ذخیره تغییرات</button>
                            </div>
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
    <style>
        .tbl_packing_form_status{
            padding: 0;
            margin: 0;
        }
        .tbl_packing_form_status td{
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
