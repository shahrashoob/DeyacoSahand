<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
    <div class="form-group">

       <div class="row">

           <div class="col-9" style="padding-left: 0px">
               @if(isset($lable) || isset($label))
                   <label id="{{$id}}_label">{!! $lable??$label??"" !!}</label>
               @endif
               <input name="{{$name??$id}}" id="{{$id}}" value="{{isset($value)?$value:""}}" type="number"
                      class="form-control" {{isset($readonly) && $readonly?"readonly":""}}  {{isset($required)?"required":""}}>


           </div>
           <div class="col-3"  style="padding-right:2px">
               @if(isset($lable) || isset($label))
                   <label >کد کشور</label>
               @endif
                   @include("component.input._select_simple",[
                       "id"=>($name??$id)."_country_id",
                       "label"=>"نوع شخصیت",
                       "option"=>$country_option["items"],
                       "val"=>$country_option["value"],
                       "text"=>$country_option["text"],
                       "class_col"=>"",
                   ])

           </div>
       </div>

    </div>



</div>
