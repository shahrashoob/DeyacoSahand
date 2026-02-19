<script>
    var ids =@php echo $ids;@endphp;
    var property_ids =@php echo $property_ids;@endphp;
    var parent_ids =@php echo $parent_ids;@endphp;
    var parent_value =@php echo $parent_value;@endphp;
var focus_id=0;
    $("#valid_property").val(property_ids);
    parent_ids.forEach(function (parent_id, k) {
        change(parent_id);
    });

    $(".property input[type=number]").change(function () {
        var id = $(this).attr("id").split("property_")[1];
        change(id);
    })

    function change(id) {

        parent_ids.forEach(function (parent_id, k) {
            if (parent_id == id) {
                not_show(ids[k]);
            }
        });
        parent_ids.forEach(function (parent_id, k) {
            if (parent_id == id) {
                if (
                    parent_value[k] == $("#property_" + id).val() ||
                    parent_value[k] == $("#property_" + id + "_auto").val()
                ) {
                    show(ids[k]);
                }
            }
        });
         $("#property_" + focus_id + "_auto").focus();
         focus_id=0;
    }

    function show(id) {
        display(id, 1);
        console.log("show(" + id + ")")
        parent_ids.forEach(function (parent_id, k) {
            if (parent_id == id) {
                if (
                    parent_value[k] == $("#property_" + id).val() ||
                    parent_value[k] == $("#property_" + id + "_auto").val()
                ) {
                    show(ids[k]);
                }
            }
        });

    }

    function not_show(id) {

        parent_ids.forEach(function (parent_id, k) {
            if (parent_id == id) {
                not_show(ids[k]);
            }
        });

        display(id, 0);
        console.log("not_show(" + id + ")")
    }

    function display(id, display) {
        console.log("display(" + id + ")=" + (display ? "block" : "none"));
        $("#property_" + id).parent().parent().css('display', display ? 'block' : 'none');
        $("#property_" + id + "_auto").parent().parent().css('display', display ? 'block' : 'none');
        if (display) {
            if (property_ids.indexOf(id) == -1) {
                property_ids.push(id);
            }
            if(focus_id ==0)
                focus_id=id;

        } else {
            if (property_ids.indexOf(id) != -1) {
                property_ids.splice(property_ids.indexOf(id), 1);
            }
        }
        $("#valid_property").val(property_ids);
    }

    $('#form1').validate({
        rules: {
            @foreach($property as $item)
            @php
                echo "'property_".$item->id."'".":'required',";
                echo "'property_".$item->id."_auto'".":'required',";
            @endphp
            @if($item->field_type_id==1)
            @php
                echo "'property_".$item->id."'".":{required:true, number:true,min:".$item->min_value.",max:".$item->max_value."},";
            @endphp
            @endif
            @endforeach
        }
    });
</script>
