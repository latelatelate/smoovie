<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 6/28/2015
 * Time: 9:34 PM
 */

use NM\Smoovie\Smoovie;

require_once "vendor/autoload.php";

// aowdjapowdjapowjda
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//awodjapwdojawd

$s = new Smoovie;

$v = '/videos/hk.mp4';


echo 'duration: ';
print_r($s->make($v)->duration());
echo '<br>';
echo 'frames: ';
print_r($s->frames());