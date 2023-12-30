<?php

namespace App\Providers;

use App\Events\ProductCreate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Models\Supplier;
use App\Observers\SupplierObserver;

use App\Events\ProductEvent;
use App\Listeners\SendProductCreateNotification;
use App\Listeners\SendProductNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // ProductCreate::class => [
        //     SendProductCreateNotification::class
        // ]
    ];

    /**
 * Determine if events and listeners should be automatically discovered.
 *
 * @return bool
 */
// public function shouldDiscoverEvents()
// {
//     return true;
// }

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(){


        Event::listen(ProductCreate::class,[SendProductCreateNotification::class,'handle']);

/*
        Event::listen(
            ProductEvent::class,
            [SendProductNotification::class, 'handle']
        );

        Event::listen(function (ProductEvent $event) {
            //
        });
*/

        // User::observe(UserObserver::class);

        Supplier::observe(SupplierObserver::class);
    }
}
