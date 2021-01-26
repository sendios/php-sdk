<?php

namespace Tests;

use Sendios\SendiosDi;
use PHPUnit\Framework\TestCase;

class SendiosDiTest extends TestCase
{
    public function testShouldCheckThatDiReturnsCorrectProperties()
    {
        $this->testProperty = array(array('test_property'));
        $di = new SendiosDi($this);
        $this->assertSame($this->testProperty, $di->testProperty);
        $this->testProperty = array(array('new test data'));
        $this->assertSame($this->testProperty, $di->testProperty);
    }
}
