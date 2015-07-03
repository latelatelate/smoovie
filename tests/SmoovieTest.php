<?php

use NM\Smoovie\Smoovie;

class SmoovieTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     */
    public function testFileNotReadableException() {
        $file = '/_files/6504/58/6332_unreadable.xlsx';
        $s = new Smoovie();

        $s->make($file);

        $this->assertException($s->make($file));
        
    }

}