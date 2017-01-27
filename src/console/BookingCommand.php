<?php namespace Panakour\Booking\Console;

use Illuminate\Console\Command;
use Panakour\Booking\BookingHotel;

class BookingCommand extends Command
{

    protected $name = 'booking:generate';

    protected $description = 'Generate hotels from booking.com files';


    public function handle(BookingHotel $hotel)
    {
        $this->info('This is going to take a while...');
        $hotel->generate($this->output);
        $this->info('Hotels generated successfully!!!');
    }

}
