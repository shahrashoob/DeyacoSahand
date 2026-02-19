@php
    $show_consumed_panel=false;
    foreach($consumed_product_list as $order_list_id=>$list){
           foreach($list as $item){
               $show_consumed_panel=true;
               break;
           }
    }
@endphp
@if($show_consumed_panel)
    <div class="col-md-12">
{{--        <form id="form2" style="display: inline"--}}
{{--              action="{{route("customer_group.buy.submit_consumed_product",$order)}}" method="post"--}}
{{--              autocomplete="off">--}}
{{--            @csrf--}}

            <div class="card">
                <div class="card-header">
                    <h5>مشخصات نوع تامین مواد اولیه </h5>
                </div>
                <div class="card-block overflow-auto">
                    <table class="table  " style="text-align: center;font-size:11px;width: 100%">
                        <thead>
                        <tr>
                            <th> ردیف</th>
                            <th>کد کالا</th>
                            <th>نام کالا</th>
                            <th>کد ماده اولیه</th>
                            <th>نام ماده اولیه</th>
                            <th>نوع تامین</th>
                        </tr>

                        </thead>
                        <tbody>
                        @php $row=1;
                        @endphp
                        @foreach($consumed_product_list_final as $order_list_id=>$list)
                            @foreach($list as $item)
                                <tr>
                                    <td>{{$row++}}</td>
                                    <td>{{$item->product->code}}</td>
                                    <td>{{$item->product->caption}}</td>
                                    <td>{{$item->material->code}}</td>
                                    <td>{{$item->material->caption}}</td>
                                    <td>
                                        @include("component.input._select_simple",[
                                       "id"=>"consumed_".$order_list_id."_".$item->material->id,
                                       "required"=>"required",
                                       "class"=>"select_class",
                                       "option"=>$contractor_supply_type_option[$order_list_id ][ $item->material_id ]["items"],
                                       "val"=>$contractor_supply_type_option[ $order_list_id ][ $item->material_id ]["value"],
                                       "text"=>$contractor_supply_type_option[ $order_list_id ][ $item->material_id ]["text"],
                                       "class_col"=>""
                                       ])
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                        </tbody>
                    </table>

                </div>
{{--                <div class="center">--}}
{{--                    @if($show_confirm)--}}
{{--                        <button type="submit" class="btn btn-primary">ثبت</button>--}}
{{--                    @else--}}
{{--                        <button type="submit" class="btn btn-primary">ثبت و ادامه</button>--}}
{{--                    @endif--}}
{{--                </div>--}}
            </div>

{{--        </form>--}}
    </div>
@endif
