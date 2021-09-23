<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class AppTest extends TestCase {

    public function testTestsAreWorking(): void {
        $a = 1;
        $b = 3;
        assertEquals(4, $a+$b);
    }
}