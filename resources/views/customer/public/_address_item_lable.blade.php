
    <div class="card">
        <div class="card-header">
            <h5> اطلاعات آدرس و تماس دریافت کننده</h5>
        </div>
        <div class="card-block overflow-auto">

            <div class="row">

                @include("component.input._lable",["id"=>"city_name", "lable"=>"کشور","value"=>$order->address->country->caption??"","class_col"=>"col-md-4"])
                @include("component.input._lable",["id"=>"city_name", "lable"=>"استان","value"=>$order->address->province->caption??"","class_col"=>"col-md-4"])

                @include("component.input._lable",["id"=>"city_name", "lable"=>"شهرستان","value"=>$order->address->city_name??"","class_col"=>"col-md-4"])

                @include("component.input._lable",["id"=>"phone", "lable"=>"شماره ثابت / نمابر ","value"=>$order->address->phone??"","class_col"=>"col-md-4"])
                @include("component.input._lable",["id"=>"mobile", "lable"=>"شماره همراه ","value"=> ($order->address->country->area_code??"").($order->address->mobile??""),"class_col"=>"col-md-4"])
                @include("component.input._lable",["id"=>"postal_code", "lable"=>"کد پستی","value"=>$order->address->postal_code??"","class_col"=>"col-md-4"])
                @include("component.input._lable",["id"=>"address", "lable"=>"نشانی ","value"=>$order->address->address??""])


            </div>

        </div>
    </div>
