<?php

namespace App\Events\Machine;

use App\Listeners\Machine\MachineAllocationListener;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Production\Production;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class   MachineAllocationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $production;
    public $machine;
    public $goods_kind;
    public $band_code;
    public $allocation_amount;
    public $allocation_sub_amount;
    public $number_of_packing_form;
    public $number_of_doffs_done;
    public $max_number_of_doffs;
    public $amount_of_each_doffs;
    public $user_id;
    public $line_product_station;
    public $packing_type_doffs;
    public $machine_allocation_id;
    public $parent_allocation_id;

    public $allocation_unit_type_id;

    public function __construct(
        Production $production,
        Machine $machine,
        $goods_kind_caption,
        $band_code,
        $allocation_amount,
        $number_of_doffs_done,
        $max_number_of_doffs,
        $amount_of_each_doffs,
        $user_id=null,
        $line_product_station_id=null,
        $packing_type_doffs=null,
        $parent_allocation_id=null,
        $machine_allocation_id=null,
        $allocation_sub_amount=null,
        $number_of_packing_form=null,
        $allocation_unit_type_id=1 // واحد اصلی کالا
    )
    {
        $test=new MachineAllocationListener();
        $this->production=$production;
        $this->machine=$machine;
      //  $this->goods_kind=GoodsKind::where("caption_en",$goods_kind_caption)->first();
        $this->band_code=$band_code;

        $this->allocation_amount=$allocation_amount;
        $this->allocation_sub_amount=$allocation_sub_amount;
        $this->number_of_packing_form=$number_of_packing_form;

        $this->number_of_doffs_done=$number_of_doffs_done;
        $this->max_number_of_doffs=$max_number_of_doffs;
        $this->amount_of_each_doffs=$amount_of_each_doffs;
        $this->user_id=$user_id;
        $this->line_product_station_id=$line_product_station_id;
        $this->packing_type_doffs=$packing_type_doffs;
        $this->parent_allocation_id=$parent_allocation_id;
        $this->machine_allocation_id=$machine_allocation_id;
        $this->allocation_unit_type_id=$allocation_unit_type_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
