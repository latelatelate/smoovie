<?php namespace NM\Smoovie;

use Exception;

class Smoovie {

    protected $cmd;
    protected $src;
    protected $mimetype;
    protected $filename;
    protected $basename;
    protected $extension;
    protected $duration;
    protected $frames;
    protected $fps;
    protected $previewPath;
    protected $thumbPath;
    protected $galleryPath;
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

    public function __construct($video)
    {
        $this->previewPath = __DIR__.'/../output/';
        $this->thumbPath = __DIR__.'/../output/thumb/';
        $this->galleryPath = __DIR__.'/../output/gallery/';
        $this->make($video);
    }

    /**
     * Returns duration of current video instance
     *
     * @throws Exception if invalid output from ffprobe
     * @return float duration in seconds.
     */
    public function duration()
    {
        if ($this->duration)
        {
            return $this->duration;
        }

        $this->cmd = 'ffprobe -v quiet -of csv=p=0 -show_entries format=duration ' . escapeshellarg($this->src) . ' 2>&1';

        exec($this->cmd, $output);

        if (!is_numeric($output[0]))
        {
            throw new Exception('Invalid output returned from ffprobe. The following output array was returned: ' . print_r($output));
        }

        $this->duration = floatval($output[0]);

        return $this->duration;
    }

    /**
     * Returns total number of frames in video
     *
     * @throws Exception if invalid ffprobe response
     * @return float total number of frames
     */
    public function frames()
    {
        if ($this->frames)
        {
            return $this->frames;
        }

        $this->cmd = "ffprobe -i " . escapeshellarg($this->src) . " -show_streams | grep -m 1 'nb_frames=' | cut -f2- -d'='";

        exec($this->cmd, $output);

        if (intval($output[0]) == 0)
        {
            throw new Exception('FFProbe returned an invalid reposnse. Output: ' . print_r($output));
        }

        $this->frames = intval($output[0]);

        return $this->frames;
    }

    /**
     * Calculate FPS of video
     *
     * @return float frames per second
     */
    public function fps()
    {
        if (!$this->frames)
        {
            $this->frames = $this->frames();
        }

        if (!$this->duration)
        {
            $this->duration = $this->duration();
        }

        $this->fps = $this->frames/$this->duration;

        return $this->fps;
    }

    /**
     * Create a trailer preview of video file. Specify optional /path/to/output.mp4,
     * start time in seconds, and total length in seconds.
     *
     * @param string $output optional output file, uses preview path by default
     * @param int $start where to start cutting from (in seconds)
     * @param int $seconds clip length (in seconds), defaults to 10
     * @return array with 200 Success or Error code + msg
     */
    public function preview($output = null, $start = null, $seconds = null)
    {
        if (!$output) {
            $file = $this->basename . '.mp4';
            $output = $this->previewPath . $file;
        }

        if (!$start) {
            $start = 0;
        }

        if (!$seconds) {
            $seconds = 10;
        }

        $cmd = 'ffmpeg -y -i '. escapeshellarg($this->src) .' -vf scale="trunc(oh*a/2)*2:480" -c:v libx264 -crf 26 -preset superfast -ss '. escapeshellarg($start) .' -t '. escapeshellarg($seconds) .' -c:a copy '. escapeshellarg($output);
        exec($cmd);

        if (!file_exists($this->previewPath . $this->basename . '.mp4'))
        {
            return [500, 'failed to create video file'];
        }

        return json_encode([200, 'success']);
    }

    /**
     * Take screen cap of ~beginning of video to use as video thumbnail
     *
     * @return array with 200 Success or Error code + msg
     */
    public function thumb()
    {
        // start at 2s to compensate for blackface err screen
        $cmd = 'ffmpeg -ss 2 -i '. $this->src .' -vframes 1 -q:v 2 ' . $this->thumbPath . $this->basename . '.jpg';
        exec($cmd);

        if (!file_exists($this->thumbPath . $this->basename . '.jpg'))
        {
            return [500, 'failed to create image file'];
        }

        return [200, 'success'];

    }

    /**
     * Create an "image gallery" from video file. Evenly grabs images
     * CAUTION: Each image grab uses a separate FFMPEG command in this build. If you grab 100 images, FFMPEG will run 100 times.
     * Don't break your server ;]. Will implement queuing in the future!
     *
     * @param int $max number of images to generate, default 24
     */
    public function gallery($max = null)
    {

        if (!$this->fps)
        {
            $this->fps = $this->fps();
        }

        if (!$this->frames)
        {
            $this->frames = $this->frames();
        }

        if (!$max)
        {
            $max = 24;
        }

        $interval = floor($this->frames/$max);
        $seconds = floor($interval/$this->fps);

        $galpath = $this->galleryPath . $this->basename . '/';
        if (!file_exists($galpath)) {
            mkdir($galpath, 0775, true);
        }

        $cmd = 'time for i in {0..'.$max.'} ; do ffmpeg -ss `echo $i*'.$seconds.' | bc` -y -i '. $this->src .' -frames:v 1 '.$galpath .'$i.jpg ; done';

        //$cmd = 'ffmpeg -y -i '. $this->src .' -vf fps=1/'.$seconds.' '.$galpath .'%d.jpg';


        //$cmd = 'ffmpeg -i '. $this->src .' -vsync 0 -vf "select=\'not(mod(n,'. $interval .'))\'" '. $galpath .'%d.jpg';

        echo $cmd;

        exec($cmd);

    }

    /**
     * Set src file and do initial validation of video.
     *
     * @throws Exception if video isn't readable or valid type.
     * @return mixed $this.
     */
    private function make($video)
    {
        $this->src = $video;

        if (!is_readable($this->src))
        {
            throw new Exception('Unable to read file');
        }

        $this->mimetype = mime_content_type($this->src);

        if (!in_array($this->mimetype, $this->allowed))
        {
            throw new Exception('Please provide a valid video file.');
        }

        $this->filename = basename($this->src);
        $tmp = explode('.', $this->filename);
        $this->basename = $tmp[0];
        $this->extension = $tmp[1];

        return $this;

    }

}