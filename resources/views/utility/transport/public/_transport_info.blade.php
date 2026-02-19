@include("component.input._lable",["id"=>"driver_firstname", "lable"=>"نام راننده","value"=>$transport->car->driver_firstname??"","class_col"=>"col-md-2"])

@include("component.input._lable",["id"=>"driver_lastname", "lable"=>"نام خانوادگی راننده","value"=>$transport->car->driver_lastname??"","class_col"=>"col-md-2"])

@include("component.input._lable",["id"=>"driver_mobile", "lable"=>"شماره همراه راننده ","value"=>$transport->car->driver_mobile??"","class_col"=>"col-md-2"])

@include("component.input._lable",["id"=>"car_plaque", "lable"=>"پلاک خودرو","value"=>$transport->car->car_plaque??"","class_col"=>"col-md-2"])

@include("component.input._lable",["id"=>"driver_firstname1", "lable"=>"نوع خودرو","value"=>$transport->car->car_type->caption??"","class_col"=>"col-md-2"])
@include("component.input._lable",["id"=>"driver_firstname1", "lable"=>"وضعیت بار","value"=>$transport->status->caption??"","class_col"=>"col-md-2"])

<div class="col-md-12">
    <div class="form-group">
        <label>
            {{$transport->transport_type_id==1?"فرم های انبار موجود در بار":"برگ های خروج موجود در بار"}}
            :

        </label>
        @foreach($transport->transport_forms as $item)

            <b>
                {{$item->form->getCode()}},
            </b>

        @endforeach
    </div>
</div>

@if(isset($weight))
    @include("component.input._lable",["id"=>"weight", "lable"=>"وزن خالص","value"=>($weight??"")." کیلوگرم","class_col"=>"col-md-2"])
    @include("component.input._lable",["id"=>"gross_weight", "lable"=>"وزن نا خالص","value"=>($gross_weight??"")." کیلوگرم","class_col"=>"col-md-2"])
    @include("component.input._lable",["id"=>"gross_weight", "lable"=>"تعداد بسته بندی","value"=>($packing_form_count??"")." عدد","class_col"=>"col-md-2"])
@endif
