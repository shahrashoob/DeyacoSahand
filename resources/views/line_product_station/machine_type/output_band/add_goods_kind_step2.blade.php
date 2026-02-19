@extends('layouts.admin._master')
@section("page_header_title"," داشبورد مدیریت ")
@section("content")
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>افزودن رسته کالایی به
                        <b>
                            {{ $machine_type_output_band->caption}}
                        </b>
                        از گروه ماشین
                        <b>
                            {{$machine_type->caption}}
                        </b>
                    </h5>
                </div>
                <div class="card-block">
                    <form id="form2"
                          action="{{route("line_product_station.machine_type.output_band.add_goods_kind_submit_step2",[$machine_type_output_band,$goods_kind])}}"
                          method="post"
                          autocomplete="off"
                          novalidate="novalidate">
                        @csrf

                        <h5>لطفا بسته بندی های مجاز {{$goods_kind->caption}}
                            را مشخص نمایید.</h5>
                        <br/>
                        <div class="col-md-12">
                            <table>

                                @foreach($goods_kind->packing_type as $item)
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="switch-data[{{$item->id}}]"
                                                   name="data[{{$goods_kind->id}}][{{$item->id}}]"
                                            >
                                            <b> {{$item->caption}}  </b>
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                            <br/>
                            <br/>

                        </div>
                        <button type="submit" class="btn btn-primary"> افزودن</button>

                    </form>

                    <div class="row">
                    </div>
                </div>
            </div>
        </div>
        <div>
            <a href="{{route("line_product_station.machine_type.output_band.index",$machine_type)}}"
               class="btn btn-outline-dark">بازگشت</a>
        </div>
    </div>

    </div>

@endsection
