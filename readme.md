#Smoovie: Simple FFMPEG/FFProbe Tools for PHP

[![Build Status](https://travis-ci.org/latelatelate/smoovie.svg?branch=master)](https://travis-ci.org/latelatelate/smoovie/)

A mini library of tools to manipulate video files with FFMPEG. Use at your own risk. This is a master alpha build.

## Installation

The recommended way to install Smoovie is through [Composer](https://getcomposer.org).

```json
{
    "require": {
        "nm/smoovie": "master"
    }
}
```

## Basic Usage

Include composer's autoload file
```php
require_once '/path/to/vendor/autoload.php';
```

Import class in your scripts
```php
use NM\Smoovie\Smoovie;
```

Have fun :)
```php
$file = '/path/to/video.mp4'
$s = new Smoovie;
$s->make($file);
$duration = $s->duration();
$frames = $s->frames();
$fps = $s->fps();
$generate_thumbnail = $s->thumb();
$vid_trailer = $s->preview($output = null, $start = null, $seconds = null);
```

## License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).