<div class="card-block">

    <div class="row">


        @foreach(($employment->personal_type_id==1)?$employment->worker->user_address: $employment->company->user_address as $item)
            <div class="col-md-6">
                <div class="row">
                    @include("component.input._lable",["id"=>"country_id","label"=>"کشور","value"=> $item->address->country->caption??""])
                    @include("component.input._lable",["id"=>"province_id","label"=>"استان","value"=> $item->address->province->caption??""])
                    @include("component.input._lable",["id"=>"city_name","label"=>"شهرستان","value"=>$item->address->city_name??""])
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    @include("component.input._lable",["id"=>"postal_code","label"=>"کدپستی","value"=>$item->address->postal_code??""])
                    @include("component.input._lable",["id"=>"mobile","label"=>"شماره همراه","value"=>$item->address->mobile??""])
                    @include("component.input._lable",["id"=>"phone","label"=>"شماره ثابت","value"=>$item->address->phone??""])
                </div>
            </div>
            @include("component.input._lable",["id"=>"address","label"=>"نشانی","value"=>$item->address->address??"","col_class"=>"col-md-12"])
            @php
                $employment_document_type=$employment->employment_document_types()->where('receive_document_step_id',2)->first();
            @endphp

            @if($panel_type=='register')
                @if($employment_document_type)
                    <a href="{{route("hr.employment.register.personal.academic_degree.download",[$employment,$employment_document_type])}}">
                        <i class="fa fa-download "></i> </a>
                @endif
            @endif
            @if($panel_type=='admin')
                @if($employment_document_type)
                    <td>
                        <a href="{{route("hr.employment.admin.confirm.upload_document.download",[$employment,$employment_document_type])}}">
                            {{$employment_document_type->document_type->caption}} </a></td>
                @endif
            @endif

        @endforeach

    </div>
</div>
