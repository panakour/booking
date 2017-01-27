<?php namespace Panakour\Booking;

class BookingHotel
{
    private $filesPath;
    private $files;
    private $townKey = 33;
    private $hotelLinkKey = 16;
    private $hotels = [];
    private $hotel;
    private $town;

    public function __construct()
    {
        $this->town = config('booking')['town'];
        $this->filesPath = config('booking')['booking_files_path'];
        $this->files = array_diff(scandir($this->filesPath), ['.', '..']);
        if (empty($this->files)) {
            throw new \Exception("There Isn't Any file");
        }
    }

    public function generate($output)
    {
        $output->progressStart(count($this->files));
        foreach ($this->files as $file) {
            $fp = fopen($this->filesPath . '/' . $file, 'r');
            if (($headers = fgetcsv($fp, 0, "\t")) !== FALSE)
                if ($headers)
                    while (($line = fgetcsv($fp, 0, "\t")) !== FALSE) {
                        $this->filterTown($line);
                        $this->addAffiliateId();
                        $this->combineHotelWithHeader($headers);
                    }
            fclose($fp);
            $output->progressAdvance();
        }
        $this->saveToFile();
        $output->progressFinish();
    }

    private function filterTown($line)
    {
        if (isset($line[$this->townKey])) {
            foreach ($this->town as $town) {
                $this->hotel[$town] = array_filter($line, function () use ($line, $town) {
                    return ($line[$this->townKey] == $town);
                });
            }
        }
    }

    private function addAffiliateId()
    {
        foreach ($this->hotel as $key => $hotel) {
            if (!empty($hotel)) {
                $this->hotel[$key][$this->hotelLinkKey] = $hotel[$this->hotelLinkKey] . '?aid=' . config('booking')['affiliate_id'];
            }
        }
    }

    private function combineHotelWithHeader($headers)
    {
        foreach ($this->hotel as $key => $hotel) {
            if ($hotel)
                if (sizeof($hotel) == sizeof($headers))
                    if ($this->hotels[][$key] = array_combine($headers, $hotel)) ;
        }
    }

    private function mergeHotelsToItsTown()
    {
        foreach ($this->town as $town) {
            foreach ($this->hotels as $hotel) {
                if (isset($hotel[$town])) {
                    $allHotels[$town][] = $hotel[$town];
                }
            }
        }
        return $allHotels;
    }

    private function saveToFile()
    {
        $hotels = $this->mergeHotelsToItsTown();
        foreach ($hotels as $key => $hotel) {
            file_put_contents(config('booking')['hotels'] . $key . '.php', '<?php return ' . var_export($hotel, true) . ";\n");

        }

    }


}