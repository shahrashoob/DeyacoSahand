<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                @if($product->supply_type_id==2)
                    <h5> افزودن تامین کننده جدید برای {{$product->fullCaption()}} </h5>
                @elseif($product->supply_type_id==4)
                    <h5> افزودن مشتری جدید برای {{$product->fullCaption()}} </h5>
                @else
                    <h5> افزودن مسیر جدید برای {{$product->fullCaption()}} </h5>
                @endif
            </div>
            <div class="card-block">

                <form id="form1" action="{{route($route_path."store",[$product,$product_creation_process])}}"
                      method="post"
                      autocomplete="off"
                      novalidate="novalidate">
                    @csrf
                    <div class="row">
                        @php $lable_value="";
                            switch ($product->supply_type_id){
                                case 2:
                                    $lable_value="تامین کننده ";
                                    break;
								case 3:
									$lable_value="مسیر پیمانکاری ";
									break;
								case 4:
									$lable_value="مسیر مشتری ";
									break;
								default:
									$lable_value="مسیر ";

								}

                                $lable_value.=($product->route()->count()+1);
                        @endphp
                        @include("component.input._text",["id"=>"caption",'label'=>"عنوان مسیر ","value"=>$lable_value])


                        <div class="col-md-6">
                            @include("component.input._aotocomplet2",[
                                "id"=>"active_status_id",
                                "label"=>" وضعیت   ",
                                "option"=>$status_option["items"],
                                "val"=>$status_option["value"],
                                "text"=>$status_option["text"],
                                "class_col"=>""
                                ])
                        </div>
                        <div class="w-100"></div>


                    </div>

                    @include($view_path."_btn_back")

                    <button type="submit" class="btn btn-primary"> ثبت و ادامه</button>

                </form>

            </div>
        </div>
    </div>

</div>
