<?php

use NM\Smoovie\Smoovie;

class SmoovieTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     */
    public function testFileNotReadableException() {
        $file = '/_files/6504/58/6332_unreadable.xlsx';
        $s = new Smoovie($file);
    }

    /**
     * @expectedException Exception
     */
    public function testFileTypeNotAllowedException() {
        $file = __DIR__.'/dummy/test.csv';
        $s = new Smoovie($file);
    }

    public function testDurationIsFloat() {
        $file = __DIR__.'/dummy/test.mp4';
        $s = new Smoovie($file);

        $duration = $s->duration();

        $this->assertInternalType('float', $duration);

    }

    public function testFramesIsInt() {
        $file = __DIR__.'/dummy/test.mp4';
        $s = new Smoovie($file);

        $duration = $s->frames();

        $this->assertInternalType('int', $duration);

    }

    public function testFpsIsFloat()
    {
        $file = __DIR__.'/dummy/test.mp4';
        $s = new Smoovie($file);

        $duration = $s->fps();

        $this->assertInternalType('float', $duration);
    }

}