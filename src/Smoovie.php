<?php namespace NM\Smoovie;

use Exception;
use SplFileInfo;

class Smoovie {

    /**
     * Stores the current ffmpeg cmd
     *
     * @var string
     */
    protected $cmd;
    protected $src;
    protected $mimetype;
    protected $duration;
    protected $frames;
    protected $allowed = [
        "video/animaflex",
        "video/x-ms-asf",
        "video/x-ms-asf-plugin",
        "application/x-troff-msvideo",
        "video/avi",
        "video/msvideo",
        "video/x-msvideo",
        "video/avs-video",
        "video/x-dv",
        "video/dl",
        "video/x-dl",
        "video/x-dv",
        "video/fli",
        "video/x-fli",
        "video/x-atomic3d-feature",
        "video/gl",
        "video/x-gl",
        "video/x-isvideo",
        "video/mpeg",
        "video/x-motion-jpeg",
        "video/quicktime",
        "video/x-sgi-movie",
        "video/x-mpeg",
        "video/x-mpeq2a",
        "video/x-qtc",
        "video/vnd.rn-realvideo",
        "video/x-scm",
        "video/vdo",
        "video/vivo",
        "video/vnd.vivo",
        "video/vosaic",
        "video/x-amt-demorun",
        "video/x-amt-showrun",
        "video/webm",
        "video/ogg",
        "video/mp4"
    ];


    public function make($video)
    {
        $this->src = $video;
        //$this->src = 'test';
        if (!is_readable($this->src))
        {
            throw new Exception('File not accesible or invalid.');
        }

        $this->mimetype = mime_content_type($this->src);

        if (!in_array($this->mimetype, $this->allowed))
        {
            throw new Exception('Please provide a valid video file.');
        }

        return $this;

    }

    /**
     * Returns duration of current video instance
     *
     * @throws Exception if video isn't invoked with make() first
     * @return float duration in seconds.
     * @return string If errors, return string w/ error
     */
    public function duration()
    {

        if ($this->duration)
        {
            return $this->duration;
        }

        if (!$this->src)
        {
            throw new Exception('Please use make(\'xxx.mp4\') before trying to get duration.');
        }

        $this->cmd = 'ffprobe -v quiet -of csv=p=0 -show_entries format=duration ' . escapeshellarg($this->src) . ' 2>&1';
        //return $this->cmd;

        exec($this->cmd, $output);

        if (!is_array($output) || empty($output))
        {
            $output = 'FFProbe returned no output ;[. Check your FFMPEG/FFPROBE install.';
        }

        $i = floatval(array_values($output)[0]);

        if (!$i && intval($i) == $i)
        {
            $output = 'Value returned wasnt a valid duration. There must have been some error rofl.';
        }

        if (is_float($i))
        {
            $output = $i;
            $this->duration = $i;
        }

        return $output;
    }

    /**
     * Returns total number of frames in video
     *
     * @throws Exception if no video included
     * @return float total number of frames
     * @return string If errors, return string w/ error
     */
    public function frames()
    {

        if ($this->frames)
        {
            return $this->frames;
        }

        if (!$this->src)
        {
            throw new Exception('Please use make(\'xxx.mp4\') before trying to get duration.');
        }

        $this->cmd = "ffprobe -i " . escapeshellarg($this->src) . " -show_streams | grep -m 1 'nb_frames=' | cut -f2- -d'='";

        exec($this->cmd, $output);

        if ($output)
        {
            $this->frames = $output[0];
        }

        return $output[0];

    }

}