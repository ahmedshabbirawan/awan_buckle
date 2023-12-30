<?php

namespace App\Listeners;

use App\Events\ProductCreate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;

class SendProductCreateNotification
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
     * @param  \App\Events\ProductCreate  $event
     * @return void
     */
    public function handle(ProductCreate $event)
    {
       // dd($event);

      //  DB::create('brands')->

        DB::table('brands')->insert(
            ['name' => 'Dell']
        );

        // echo 'I am send product create notification';
    }
}
