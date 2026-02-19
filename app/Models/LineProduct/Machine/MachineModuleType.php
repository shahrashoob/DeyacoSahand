<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\GoodsKind;
use Database\Seeders\LineProductStation\MachineModuleTypeSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineModuleType extends Model {
    use HasFactory;

    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class );
    }

    public static function getChecklist( $machine_module_type_id, $checklist_code ) {

        $checklist=  MachineModuleTypeSeeder::$checklist;
        return $checklist[ $machine_module_type_id ][ $checklist_code ];


    }


}
