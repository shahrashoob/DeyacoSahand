
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">

                <div class="row d-flex align-items-center">
                    <div class="col-auto">
                        <div class="custom-control custom-radio">
                            <input {{count($car_list)==0 ?"":"checked"}}  value="0" type="radio"
                                   class="custom-control-input" id="old_car"
                                   name="car_type"
                                   required="">
                            <label class="custom-control-label" for="old_car"></label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="col-md-6">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"car_id",
                                    "label"=>"لیست خودور های قبلی  ",
                                    "option"=>$car_option["items"],
                                    "val"=>"",
                                    "text"=>"",
                                    "class_col"=>""
                                    ])
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-block">
                <div class="row d-flex align-items-center">
                    <div class="col-auto">
                        <div class="custom-control custom-radio">
                            <input {{count($car_list)==0 ?"checked":""}}  value="0" type="radio"
                                   class="custom-control-input" id="new_car" name="car_type"
                                   required="">
                            <label class="custom-control-label" for="new_car"></label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row">

                            <div class="col-md-2">
                                @include("component.input._aotocomplet2",[
                                    "id"=>"car_type_id",
                                    "label"=>"نوع خودرو ",
                                    "option"=>$car_type_option["items"],
                                    "val"=>$car_type_option["value"],
                                    "text"=>$car_type_option["text"],
                                    "class_col"=>""
                                    ])
                            </div>


                            @include("component.input._text",["id"=>"driver_firstname", "lable"=>"نام راننده","value"=>$driver_firstname??"","class_col"=>"col-md-2"])

                            @include("component.input._text",["id"=>"driver_lastname", "lable"=>"نام خانوادگی راننده","value"=>$driver_lastname??"","class_col"=>"col-md-2"])

                            @include("component.input._text",["id"=>"driver_mobile", "lable"=>"شماره همراه راننده ","value"=>$driver_mobile??"","class_col"=>"col-md-2"])

                            @include("component.input._text",["id"=>"car_plaque", "lable"=>"پلاک خودرو","value"=>$driver_mobile??"","class_col"=>"col-md-2"])

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

<script>
    function setReadonly() {
        readonly = !$("#new_car").is(":checked");
        $("#car_type_id_auto").prop("readonly", readonly);
        $("#driver_firstname").prop("readonly", readonly);
        $("#driver_lastname").prop("readonly", readonly);
        $("#driver_mobile").prop("readonly", readonly);
        $("#car_plaque").prop("readonly", readonly);

        $("#car_id_auto").prop("readonly", !readonly );

        $("#country_id_auto").prop("required", !readonly ? "required" : "");

        $("#car_id_auto").prop("readonly", !readonly );


        $("#car_type_id_auto").prop("required", !readonly? "required" : "");
        $("#driver_firstname").prop("required", !readonly? "required" : "");
        $("#driver_lastname").prop("required", !readonly? "required" : "");
        $("#driver_mobile").prop("required", !readonly? "required" : "");
        $("#car_plaque").prop("required", !readonly? "required" : "");

        $("#car_id_auto").prop("required", readonly ? "required" : "");

    }

    $(".custom-control-input").click(function () {
        setReadonly();

    })
    setReadonly();
</script>
