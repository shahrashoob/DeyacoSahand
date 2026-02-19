<div class="row">
    <div class="col-sm-12">
        <form id="form1" action="{{route($route_path."submit",[$product,$product_creation_process])}}" method="post"
              autocomplete="off"
              novalidate="novalidate">
            @csrf
            <div class="row">

                &nbsp;
                @include("component.input._radio_box01",["id"=>"have_testing_before_production","label"=>"آیا کالا قبل از تولید تست دارد؟","value"=>$product->have_testing_before_production])
                @include("component.input._radio_box01",["id"=>"testing_is_on_line_production_checkbox","label"=>"آیا تست بر روی مسیر محصول انجام می شود؟","value"=>$product->testing_is_on_line_production?1:0])

                @include("component.input._number",["id"=>"testing_is_on_line_production",'label'=>"شماره اولویت خط تولید برای انجام تست","value"=>$product->testing_is_on_line_production])

                @include("component.input._number",["id"=>"testing_amount",'label'=>"مقدار تست کالا (".($product->unit->caption).")","value"=>$product->testing_amount])

            </div>
            <br/>
            <br/>
            @include($view_path."_btn_list")

        </form>
    </div>

</div>
<script>
    $("#have_testing_before_production_1,#have_testing_before_production_0,#testing_is_on_line_production_checkbox_1,#testing_is_on_line_production_checkbox_0").change(function (){
       change_value();
    })
   function change_value(){
        if ($("#have_testing_before_production_1").is(":checked")) {
            $("#testing_is_on_line_production_checkbox_1").parent().css("display","")
            $("#testing_is_on_line_production").parent().css("display","")
            $("#testing_amount").parent().css("display","")

            if ($("#testing_is_on_line_production_checkbox_1").is(":checked")) {
                $("#testing_is_on_line_production").parent().css("display","")
                $("#testing_amount").parent().css("display","")
            } else {
                $("#testing_is_on_line_production").val("");
                $("#testing_is_on_line_production").parent().css("display","none")
                $("#testing_amount").parent().css("display","none")
            }
        } else {

            $("#testing_is_on_line_production_checkbox_1").parent().css("display","none")
            $("#testing_is_on_line_production").parent().css("display","none")
            $("#testing_amount").parent().css("display","none")


        }


    }
    change_value();
</script>
