<div class="row">
    @if(count($property) == 0)
        <div class="col-sm-12">
            <div class="alert alert-warning">
                برای محصول انتخاب شده هیچ نوع مشخصه ای وجود ندارد.
            </div>

        </div>
    @else

        <div class="col-sm-12">

                <div class="row">
                    @foreach($property as $item)
                        @if(!isset($property_value[$item->id]))
                            @continue
                        @endif
                        @switch($item->field_type_id)
                            @case(1)
                            @case(2)
                                @include(
                                       "component.input._lable",
                                       ["id"=>"property_".$item->id,
                                       'label'=>($item->caption."(".($item->special_unit->caption??"").")"),
                                       "class_col"=>"col-md-4",
                                       "value"=>$property_value[$item->id]??""
                                   ])
                                @break

                            @case(3)
                            @case(5)
                                @include(
                                 "component.input._lable",
                                 ["id"=>"property_".$item->id,
                                 'label'=>$item->caption,
                                 "class_col"=>"col-md-4 property",
                                 "value"=>$property_option[$item->id]["text"]??"",

                             ])


                                @break

                            @case(4)
                                <div class="col-md-6">
                                    <label>{{$item->caption."(تصویر)"}}</label>
                                    <br/>
                                    @if(isset($property_value[$item->id]))
                                        <a class="btn btn-sm btn-primary" target="_blank"
                                           href="{{route("utility.file.product.show_property",[$product,$item])}}"
                                           style="font-size: 12px">مشاهده تصویر</a>
                                    @endif

                                </div>


                                @break

                        @endswitch



                    @endforeach


                </div>


        </div>
    @endif
        @include($view_path."_btn_list")
</div>
