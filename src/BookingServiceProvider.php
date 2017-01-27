<?php namespace Panakour\Booking;

use Illuminate\Support\ServiceProvider;
use Panakour\Booking\Console\BookingCommand;

class BookingServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/booking.php' => config_path('booking.php'),
        ]);
    }

    public function register()
    {
        $this->commands([
            BookingCommand::class
        ]);
    }

}