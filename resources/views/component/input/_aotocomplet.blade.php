<div class="aotoComplet ">
    @if(isset($label))
        <label class="text-white">
            {{$label??""}}
        </label>
        @include('component.input._attention')
    @endif


    <div class=" ">
        <input type="text"
               name="{{$id}}_auto"
               id="{{$id}}_auto"
               class="form-control immybox immybox_witharrow"
        />
    </div>
</div>

<input type="hidden" name="{{$id}}" id="{{$id}}" class="form-control" value="{!! $val??"" !!}"/>

<script>
    var {{$id}}_Choses =<?php echo json_encode( $option ); ?>;

    $('#{{$id}}_auto').immybox({
        choices: {{$id}}_Choses,
        formatChoice: function (query) {
            //
            return "" + query.text;

        },
    });
    $("#{{$id}}").val('{!! $val??"" !!}');

    $('#{{$id}}_auto').val('{!! $text??"" !!}');

    $('#{{$id}}_auto').blur(function () {

        var item = $('#{{$id."_auto"}}').val();

        for (var i = 0; i < {{$id}}_Choses.length; i++) {


            if ({{$id}}_Choses[i]["text"] == item) {
                $('#{{$id}}').val({{$id}}_Choses[i]["value"]);

            }
        }

        @if(isset($my_function))
            @php echo $my_function; @endphp
        @endif
    });
</script>
