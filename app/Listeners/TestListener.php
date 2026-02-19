<?php

namespace App\Listeners;

use App\Events\TestEvent;
use App\Models\Utility\Unit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TestListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TestEvent $test_event)
    {
        //
        Unit::insert(["caption"=>$test_event->product]);
    }
}
