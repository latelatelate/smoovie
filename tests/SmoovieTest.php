<?php

use NM\Smoovie\Smoovie;

class SmoovieTest extends PHPUnit_Framework_TestCase {

    public function testNachoHasCheese()
    {
        $nacho = new Smoovie;
        $this->assertTrue($nacho->hasCheese());

    }

}