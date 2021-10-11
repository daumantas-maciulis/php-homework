<?php declare(strict_types=1);

namespace AppTest;

use App\Service\Printer;
use PHPUnit\Framework\TestCase;

class PrinterTest extends TestCase
{
    public function testWriteLn(): void
    {
        $expected = "SomeLine\n";

        $printer = new Printer();

        $this->expectOutputString($expected);
        $printer->writeLn('SomeLine');
    }
}
