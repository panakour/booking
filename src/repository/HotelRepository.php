<?php namespace Panakour\Booking\Repository;

use Illuminate\Pagination\LengthAwarePaginator;
use League\Flysystem\Exception;

class HotelRepository
{
    private $hotels;

    public function __construct($town)
    {
        $this->hotels = include config('booking')['hotels'] . $town . '.php';
    }

    public function paginated($page, $length)
    {
        $offset = ($length * $page) - $length;
        $partOfHotels = array_slice($this->hotels, $offset, $length);
        $paginatedItems = new LengthAwarePaginator($partOfHotels, count($this->hotels), $length);
        return $paginatedItems;
    }

    public function view($hotel)
    {
        $key = array_search($hotel, array_column($this->hotels, 'id'));
        $hotel = $this->hotels[$key];
        return $hotel;
    }

    public function randomStarHotels($maxResult)
    {
        $hotels = array_filter($this->hotels, function ($hotel) {
            return ($hotel['class'] == '5.0' || $hotel['class'] == '4.0' || $hotel['class'] == '3.0');
        });
        $numberOfHotels = count($hotels);
        if($numberOfHotels < $maxResult) {
            throw new Exception("Hotels are only: $numberOfHotels and you have select to get $maxResult");
        }
        $randomHotelKeys = array_rand($hotels, $maxResult);
        shuffle($randomHotelKeys);

        foreach ($randomHotelKeys as $key) {
            $randomlyHotels[] = $hotels[$key];
        }
        return $randomlyHotels;
    }


}
