<?php

namespace App\Models\LineProduct\Machine;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineEventLog extends Model {
    use HasFactory;
    protected $table = "machine_event_logs";
    protected $fillable = [
        "machine_id",
        "machine_event_type_id",
        "contour_1_value",
        "contour_2_value",
        "contour_3_value",
        "contour_4_value",
        "contour_5_value",
        "user_id",
        "shift_work_id"
    ];

    public function checkMinContour( $value1, $value2, $value3, $value4, $value5 ) {
        if ( isset( $value1 ) && isset( $this->contour_1_value ) && $value1 < $this->contour_1_value ) {
            return false;
        }
        if ( isset( $value2 ) && isset( $this->contour_2_value ) && $value2 < $this->contour_2_value ) {
            return false;
        }
        if ( isset( $value3 ) && isset( $this->contour_3_value ) && $value3 < $this->contour_3_value ) {
            return false;
        }
        if ( isset( $value4 ) && isset( $this->contour_4_value ) && $value4 < $this->contour_4_value ) {
            return false;
        }
        if ( isset( $value5 ) && isset( $this->contour_5_value ) && $value5 < $this->contour_5_value ) {
            return false;
        }

        return true;
    }
}
