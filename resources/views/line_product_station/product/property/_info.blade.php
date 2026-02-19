<div class="row">
    @if(count($property) == 0)
        <div class="col-sm-12">
            <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}" method="post"
                  autocomplete="off"
                  novalidate="novalidate">
                @csrf
                <div class="alert alert-warning">
                    برای محصول انتخاب شده هیچ نوع مشخصه ای وجود ندارد.
                </div>
                @include($view_path."_btn_list_1")
            </form>
        </div>
    @else

        <div class="col-sm-12">
            <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}" method="post"
                  autocomplete="off"
                  novalidate="novalidate">
                @csrf
                <div class="row">
                    @foreach($property as $item)
                        @switch($item->field_type_id)
                            @case(1)
                                @include(
                                        "component.input._number",
                                        ["id"=>"property_".$item->id,
                                        'label'=>($item->caption."(".($item->special_unit->caption??"").")".($item->required?" <span class='text-danger'>*</span> ":"")),
                                        "class_col"=>"col-md-6 property",
                                        "value"=>$property_value[$item->id]??""
                                    ])
                                @break
                            @case(2)
                                @include(
                                       "component.input._text",
                                       ["id"=>"property_".$item->id,
                                       'label'=>($item->caption."(".($item->special_unit->caption??"").")"),
                                       "class_col"=>"col-md-6",
                                       "value"=>$property_value[$item->id]??""
                                   ])
                                @break
                            @case(3)
                            @case(5)
                                @include(
                                 "component.input._aotocomplet2",
                                 ["id"=>"property_".$item->id,
                                 'label'=>$item->caption,
                                 "class_col"=>"col-md-6 property",
                                 "option"=>$property_option[$item->id]["items"],
                                 "val"=>$property_option[$item->id]["value"],
                                 "text"=>$property_option[$item->id]["text"],
                                 "my_function"=>"change(".$item->id.");"
                             ])


                                @break

                            @case(4)
                                <div class="col-md-6">
                                    <label>{{$item->caption."(تصویر)"}}</label>
                                    <a
                                            href="{{route($route_path."delete",[$product,$item])}}"
                                            onclick="return confirm('آیا از حذف اطمینان دارید؟')"
                                            style="font-size: 12px"><i class="fa fa-trash text-danger"></i> </a>
                                    <br/>
                                    @if(isset($property_value[$item->id]))
                                        <a class="btn btn-sm btn-primary" target="_blank"
                                           href="{{route("utility.file.product.show_property",[$product,$item])}}"
                                           style="font-size: 12px">مشاهده تصویر</a>
                                    @endif
                                    <a class="btn btn-sm btn-primary"
                                       href="{{route($route_path."upload_image",[$product,$item])}}"
                                       style="font-size: 12px">بارگذاری تصویر</a>
                                </div>


                                @break

                        @endswitch

                        <div class="w-100"></div>

                    @endforeach

                    @include($view_path."_btn_list_2")
                </div>

                @include("component.input._hidden",["id"=>"valid_property","value"=>""])
            </form>
        </div>
    @endif
</div>
