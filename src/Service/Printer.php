<?php declare(strict_types=1);

namespace App\Service;

class Printer
{
    public function write(string $string): void
    {
        echo $string;
    }

    public function writeLn(string $string): void
    {
        echo $this->write($string) . "\n";
    }

    public function writeArr(array $array): void
    {
        print_r($array);
    }
}
