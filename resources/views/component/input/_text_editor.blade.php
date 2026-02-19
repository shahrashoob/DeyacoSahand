<div class="col-md-12">
        <label class="control-label ">{{$label}}</label>
        <div class="m-b-30">
            <textarea  id="{{$id}}Div"  class="form-control " >{!! $value??"" !!}</textarea>
            <input type="hidden"  id="{{$id}}" name="{{$id}}" class="form-control "  />
        </div>
    </div>
<script>
</script>
{{--<script>--}}
{{--    $('#form1').submit(function () {--}}
{{--        $("#address").val($("#addressDiv").Editor("getText"));--}}

{{--    });--}}
{{--    $("#addressDiv").Editor();--}}
{{--    $(".Editor-editor").html("{{$item->address}}")--}}
{{--</script>--}}

{{--<link rel="stylesheet" href="{{asset('assets/plugins/editor/editor.css')}}"/>--}}
{{--<script src="{{asset('assets/plugins/editor/editor.js')}}"></script>--}}
