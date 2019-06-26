<?php

namespace App\Modules\Product\Listeners;

use App\Modules\Product\Events\SampleEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SampleListener
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
     * @param  SampleEvent  $event
     * @return void
     */
    public function handle(SampleEvent $event)
    {
        //
    }
}
