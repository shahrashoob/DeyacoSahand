@php $warehouse_first=$warehouse_list->first();@endphp

<div class="col-md-12" id="warehouse_shelving_list"
     style="text-align: right; direction: rtl; background-color: rgba(234,231,231,0.66)">
    <ul class="nav nav-tabs" role="tablist">
        @foreach($warehouse_list as  $warehouse)

            <li class="nav-item">
                <a class="nav-link  text-uppercase {{$warehouse_first->id == $warehouse->id ? "active show":""}}"
                   id="warehouse_{{$warehouse->id}}-tab" data-toggle="tab"
                   href="#warehouse_{{$warehouse->id}}"
                   role="tab" aria-controls="warehouse_{{$warehouse->id}}" aria-selected="false">
                    {{$warehouse->caption}}
                </a>
            </li>

        @endforeach
    </ul>

    <div class="tab-content">
        @foreach($warehouse_list as  $warehouse)

            <div class="tab-pane fade {{$warehouse_first->id == $warehouse->id ? "active show":""}} "
                 id="warehouse_{{$warehouse->id}}" role="tabpanel"
                 aria-labelledby="home-tab">

                    <div class="row">
                        @if(isset($warehouse_shelving_option["items"][$warehouse->id]))
                        <div class="col-md-9" data-select2-id="119">

                            @include("component.input.select2._select2",[
                           "id"=>"warehouse".$warehouse->id."_shelving_type_ids",
                           "label"=>"لیست محل های مجاز کالا در ".$warehouse->caption." بر اساس طبقه بندی",
                           "option"=>$warehouse_shelving_type_option["items"][$warehouse->id],
                           "class_col"=>""
                           ])

                        </div>

                        <div class="col-md-9" data-select2-id="119">

                            @include("component.input.select2._select2",[
                           "id"=>"warehouse".$warehouse->id."_shelving_ids",
                           "label"=>"لیست محل های مجاز کالا در ".$warehouse->caption. " بر اساس محل",
                           "option"=>$warehouse_shelving_option["items"][$warehouse->id],
                           "class_col"=>""
                           ])

                        </div>
                        @endif
                    </div>


            </div>

        @endforeach
    </div>

</div>