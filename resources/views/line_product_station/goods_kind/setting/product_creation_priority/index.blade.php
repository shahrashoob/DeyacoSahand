@extends('layouts.admin._master',["keypress_enable"=>1])
@section("page_header_title","کارتابل  مدیریت ")
@section("content")
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <h5>تنظیمات طراحی کالا برای {{$goods_kind->caption}}</h5>
                </div>
                <div class="card-body p-0">
                    <form id="form1"
                          action="{{route("line_product_station.goods_kind.setting.product_creation_priority.submit",$goods_kind)}}"
                          method="post"
                          novalidate="novalidate">
                        @csrf


                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-styling">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>کد عملیات</th>
                                            <th> عملیات</th>
                                            <th> توضیحات</th>
                                            <td>عملیات بعدی<br/> در صورت تایید</td>
                                            <td>پست جهت اطلاع رسانی <br/> در صورت تایید</td>
                                            <td>عملیات بعدی<br/> در صورت عدم تایید</td>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        @php $row=0;@endphp
                                        @foreach($priority_setting as $item)
                                            @include("component.input._hidden",[
                                                      "id"=>"priority_".$item->button_id,
                                                      'value'=> $item->id??""
                                                      ])
                                            <tr>
                                                <td>{{++$row}}</td>
                                                <td>
                                                    {{$item->button_id}}
                                                </td>
                                                <td>
                                                    {{$item->button->caption??$item->button_id}}
                                                </td>

                                                <td>

                                                    @include("component.input._text",[
                                                          "id"=>"description_".$item->button_id,
                                                          "label"=>"",
                                                          "class_col"=>"txt_input",
                                                          'value'=> $item->description??""
                                                          ])
                                                </td>
                                                <td>

                                                    @include("component.input._select_simple",[
                                                        "id"=>"next_status_id_".$item->button_id,
                                                        "label"=>"",
                                                        "option"=>$next_status_option_list[$item->button_id]["items"],
                                                        "val"=>$next_status_option_list[$item->button_id]["value"],
                                                        "text"=>$next_status_option_list[$item->button_id]["text"],
                                                        "class_col"=>""
                                                        ])

                                                </td>
                                                <td>
                                                    @include("component.input._select_simple",[
                                                          "id"=>"post_id_".$item->button_id,
                                                          "label"=>"",
                                                          "option"=>$post_option_list[$item->button_id]["items"],
                                                          "val"=>$post_option_list[$item->button_id]["value"],
                                                          "text"=>$post_option_list[$item->button_id]["text"],
                                                          "class_col"=>""
                                                          ])
                                                </td>
                                                <td>
                                                    @include("component.input._select_simple",[
                                                          "id"=>"before_status_id_".$item->button_id,
                                                          "label"=>"",
                                                          "option"=>$before_status_option_list[$item->button_id]["items"],
                                                          "val"=>$before_status_option_list[$item->button_id]["value"],
                                                          "text"=>$before_status_option_list[$item->button_id]["text"],
                                                          "class_col"=>""
                                                          ])
                                                </td>

                                            </tr>
                                        @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                @include("component.input._radio_box01",["id"=>"has_sampling_required_in_product_creation",
                                                              "label"=>"آیا تولید نمونه آزمایشگاهی برای کالا الزامی است؟",
                                                              "label0"=>"خیر",
                                                              "label1"=>"بله",
                                                              "value"=>$goods_kind->has_sampling_required_in_product_creation ??"",
                                                              ])


                            </div>

                        </div>

                        <div class="col-md-12">
                            <a href="{{route("line_product_station.goods_kind.index")}}"
                               class="btn btn-outline-dark">بازگشت</a>
                            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                        </div>
                        <br/>
                    </form>
                    <div class="float-left">
                        نمايش رکوردهای
                        <b>{{$priority_setting->firstItem()}}</b>
                        تا
                        <b>{{$priority_setting->lastItem()}}</b>
                        از
                        <b>{{$priority_setting->total()}}</b>
                        رکورد موجود
                    </div>
                </div>
                <div class="text-center">
                    {{$priority_setting->links('pagination::bootstrap-4')}}
                </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section("styles")
    <style>
        .txt_input {
            margin-top: -20px;
        }
    </style>
    @include("component.input.datepicker._script")
    <script src="{{asset("assets/plugins/autocomplet/jquery.immybox.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/plugins/autocomplet/immybox.css")}}"/>
@endsection
