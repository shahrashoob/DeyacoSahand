@foreach($address_list as $item)
    @if($item->address)
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-right">
                        <div class="btn-group card-option">

                            <a href="{{route((isset($route_path)?$route_path: "customer_group.buy.address_edit"),[$order,$item->address_id??0])}}">
                                <i class="fa fa-edit"></i>
                            </a>


                        </div>
                    </div>
                    <div class="row d-flex align-items-center">
                        <div class="col-auto">
                            <div class="custom-control custom-radio">
                                <input
                                    {{ $order->address_id == $item->address_id || !$order->address_id ?"checked":""}} value="{{$item->address_id}}"
                                    type="radio" class="custom-control-input" id="address_{{$item->id}}"
                                    name="address_id"
                                    required="">
                                <label class="custom-control-label" for="address_{{$item->id}}"></label>
                            </div>
                        </div>
                        <div class="col">

                            <h6 class="f-w-300">{{$item->address->country->caption??""}}
                                -{{$item->address->province->caption??""}}- {{$item->address->city_name??""}}
                                - {{$item->address->address??""}} </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <i class="fa fa-envelope"></i> {{$item->address->postal_code??""}}<br/>
                                </div>
                                <div class="col-md-4">
                                    <i class="fa fa-phone"></i> {{$item->address->phone??""}}<br/>
                                </div>
                                <div class="col-md-4">
                                    <i class="fa fa-mobile-alt"></i> 0{{$item->address->mobile??""}}<br/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
@endforeach

@php $default_address=count($address_list)==1?$order->customer->getDefaultAddress():null; @endphp
<div class="col-md-12">
    <div class="card">
        <div class="card-block">
            <div class="row d-flex align-items-center">
                <div class="col-auto">
                    <div class="custom-control custom-radio">
                        <input {{count($address_list)==0 ?"checked":""}}  value="0" type="radio"
                               class="custom-control-input" id="new_address" name="address_id"
                               required="">
                        <label class="custom-control-label" for="new_address"></label>
                    </div>
                </div>
                <div class="col">
                    <div class="row">

                        <div class="col-md-2">
                            @include("component.input._aotocomplet2",[
                                "id"=>"country_id",
                                "label"=>"کشور ",
                                "option"=>$country_option["items"],
                                "val"=>$country_option["value"],
                                "text"=>$country_option["text"],
                                "class_col"=>""
                                ])
                        </div>

                        <div class="col-md-2">
                            @include("component.input._aotocomplet2",[
                                "id"=>"province_id",
                                "label"=>"استان ",
                                "option"=>$province_option["items"],
                                "val"=>$default_address->province_id??"",
                                "text"=>$default_address->province->caption??"",
                                "class_col"=>""
                                ])
                        </div>

                        @include("component.input._text",["id"=>"city_name", "lable"=>"شهرستان","value"=>$default_address->city_name??"","class_col"=>"col-md-2"])

                        @include("component.input._text",["id"=>"phone", "lable"=>"شماره ثابت / نمابر ","value"=>$default_address->phone??"","class_col"=>"col-md-2"])

                        @include("component.input._text",["id"=>"mobile", "lable"=>"شماره همراه (بدون صفر) ","value"=>$default_address->mobile??"","class_col"=>"col-md-2"])

                        @include("component.input._text",["id"=>"postal_code", "lable"=>"کد پستی","value"=>$default_address->postal_code??"","class_col"=>"col-md-2"])
                        <div class="w-100"></div>
                        @include("component.input._textarea",["id"=>"address", "lable"=>"نشانی ","value"=>$default_address->address??""])
                        <div class="w-100"><br/></div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>

