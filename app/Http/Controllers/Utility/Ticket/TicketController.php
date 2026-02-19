<?php

namespace App\Http\Controllers\Utility\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Ticket\TicketType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(){

    }

    public function create(TicketType $ticket_type){

        if(!isset($ticket_type->id)){
            $list=TicketType::all();
            return view("utility.ticket.ticket.select_ticket_type",compact("list"));
        }

        $machine_option=Option::get("machine");
        $cost_center_option=Option::get("cost_center");
        return view("utility.ticket.ticket.create",compact("ticket_type","machine_option","cost_center_option"));
    }
    public function store(TicketType $ticket_type, Request $request){
        return $request->all();


    }
}
