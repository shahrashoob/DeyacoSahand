 <input name="{{$id}}" id="{{$id}}" value="{{isset($value)?$value:""}}" 
  min=0 
 
 {{isset($max)?"max=".$max:""}}
 {{isset($readonly)?"readonly":""}} {{isset($required)?"required":""}} style="width: 80px" type="number" >