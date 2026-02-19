<div class="card-body ">
    <form id="form_degree_{{$band_code}}"

    >
        @csrf

        <div class="alert alert-info  col-md-12">
            لطفا درجه نهایی کالا را مشخص نمایید.
        </div>


        @include("component.input._select",[
                "id"=>"degree_id_".$band_code,
                "label"=>"درجه کالا",
                "option"=>$degree_option["items"],
                "class_col"=>"col-md-6"
                ])
        <br/>

        <div class="col-md-12">
            <button id="btn_degree" data-band_code='{{$band_code}}' type="submit" class="btn btn-success ">
                تایید و ادامه
            </button>
        </div>
    </form>
    <script>
        $('#form_degree_{{$band_code}}').validate({
            rules: {
                "degree_id": "required",

            }
        });
        $("#btn_degree").click(function () {
            var property_values = [];

            property_values = $("#degree_id_{{$band_code}}").val();


            var validate = $("#form_degree_{{$band_code}}").validate().form();
            if (validate) {
                add_fault(
                    "form_degree",
                    "",
                    "",
                        {{$band_code}},
                    property_values,
                    ""
                )
            }
            return false;

        })
    </script>
</div>