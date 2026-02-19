<?php

namespace App\Models\LineProduct\Product\Waste;

use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WasteCollectionMachine extends Model {
    use HasFactory;
    protected $table="waste_collection_machine";
    protected $fillable=["waste_collection_id","machine_id","allocation_id","machine_log_id"];

    public function waste_collection() {
        return $this->belongsTo( WasteCollection::class );
    }
    public function machine() {
        return $this->belongsTo( Machine::class);
    }
    public function allocaiton() {
        return $this->belongsTo( Allocation::class);
    }
    public function machine_log() {
        return $this->belongsTo( MachineLog::class);
    }

}
