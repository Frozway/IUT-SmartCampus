<?php

// tests/MathTest.php

use PHPUnit\Framework\TestCase;

class MathTest extends TestCase
{
    public function testAddition()
    {
        $result = 1 + 1;
        $this->assertEquals(2, $result);
    }
}