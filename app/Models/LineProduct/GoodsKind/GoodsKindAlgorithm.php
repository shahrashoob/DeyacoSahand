<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\LineProduct\GoodsKind;
use App\Models\Post\PostUser;
use App\Models\Utility\Algorithm\Algorithm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindAlgorithm extends Model
{
    use HasFactory;

    protected $table = "goods_kind_algorithm";
    protected $fillable = [
        "goods_kind_id",
        "algorithm_id",
        "supply_type_id",
        "production_card_allocation_algorithm_id",
        "production_card_create_algorithm_id",
    ];

    public function goods_kind()
    {
        return $this->belongsTo(GoodsKind::class);
    }
    public function production_card_allocation_algorithm()
    {
        return $this->belongsTo(Algorithm::class,"production_card_allocation_algorithm_id");
    }    public function production_card_create_algorithm()
    {
        return $this->belongsTo(Algorithm::class,"production_card_create_algorithm_id");
    }

}
