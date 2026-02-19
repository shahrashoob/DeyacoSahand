<div class="col-md-12">
    <div class="row">

        <div class="w-100"></div>


                <div class="w-100"></div>
                <div class="col-md-6">
                    @include("component.input._select",[
                        "id"=>"software_system_id",
                        "label"=>"نام سامانه جامع  ",
                        "option"=>$software_system_option["items"],
                        "val"=>$software_system_option["value"],
                        "text"=>$software_system_option["text"],
                        "class_col"=>""
                        ])
                </div>
        <div class="w-100"></div><br/>

        @include("component.input._text",["id"=>"api_url","label"=>"آدرس سامانه","class_col"=>"col-md-6 offset-md-6 software","value"=>$customer->api_url])

        @include("component.input._text",["id"=>"api_username","label"=>"نام کاربری","class_col"=>"col-md-6 offset-md-6 software","value"=>$customer->api_username])

        @include("component.input._password",["id"=>"api_password","label"=>"کلمه عبور","class_col"=>"col-md-6 offset-md-6 software","value"=>$customer->api_password])

        @include("component.input._text",["id"=>"api_key","label"=>"ApiKey","class_col"=>"col-md-6 offset-md-6 software","value"=>$customer->api_key??""])

    </div>

</div>









