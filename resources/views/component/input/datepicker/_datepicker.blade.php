<div class="{{isset($class_col)?$class_col:"col-md-6 offset-md-6"}}">
    <div class="form-group">
        <label>{{$lable??$label??""}}</label>
        <input  id="{{$id}}"  name="{{$id}}_value"  type="text" value="{{isset($value)&& $value?jdate( \Carbon\Carbon::parse($value)->timestamp)->format(isset($formatDate_jalali)?$formatDate_jalali:'%Y/%m/%d'):""}}" class="form-control" {{isset($readonly) && $readonly?"readonly":""}}/>
        <input type="hidden" name="{{$id}}" id="{{$id}}_hidden" value="{{isset($value)?$value:""}}" />
    </div>
</div>
<script type="text/javascript">
        $("#{{$id}}").persianDatepicker({
            onSelect: function () {
                {{--alert($("#{{$id}}").attr("data-gdate"));--}}
                $("#{{$id}}_hidden").val($("#{{$id}}").attr("data-gdate"));
            },
            cellWidth: 42,
            cellHeight: 26,
            fontSize: 14,
            formatDate:"{{isset($formatDate)?$formatDate:"YYYY/MM/DD"}}"
        });
</script>
