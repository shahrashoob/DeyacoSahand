<div class="card-block">

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                @foreach($employment->worker->user_address as $item)
                    @include("component.input._lable",["id"=>"country_id","label"=>"کشور","value"=> $item->address->country->caption??""])
                    @include("component.input._lable",["id"=>"province_id","label"=>"استان","value"=> $item->address->province->caption??""])
                    @include("component.input._lable",["id"=>"city_name","label"=>"شهرستان","value"=>$item->address->city_name??""])
                    @include("component.input._lable",["id"=>"address","label"=>"نشانی","value"=>$item->address->address??""])
                    @include("component.input._lable",["id"=>"postal_code","label"=>"کدپستی","value"=>$item->address->postal_code??""])
                    @include("component.input._lable",["id"=>"mobile","label"=>"شماره همراه","value"=>$item->address->mobile??""])
                    @include("component.input._lable",["id"=>"phone","label"=>"شماره ثابت","value"=>$item->address->phone??""])

                @endforeach
            </div>
        </div>
    </div>
</div>
