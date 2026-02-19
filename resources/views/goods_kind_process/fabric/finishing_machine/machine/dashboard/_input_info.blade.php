@include("component.formatDecimal9")

@include("goods_kind_process.fabric_raw.jacquard.machine.dashboard._input_info",[
    "allow_injection"=>
    $post_user->checkButtonPermission("fabric.finishing_machine.machine.injection_of_material.index")
    &&
    in_array(\Illuminate\Support\Str::substr($machine->production_status_id,-3),$controller_info["06"]["enable_status"])
    &&
    !$value_202
    ,
"injection_route"=>"fabric.finishing_machine.machine.injection_of_material.submit"]
)