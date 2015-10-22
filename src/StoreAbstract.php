<?php namespace Teepluss\Up2;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;

class StoreAbstract 
{

    /**
     * Config from uploader.
     *
     * @var array
     */
    public $config;

    /**
     * Request.
     *
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * Files.
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * File input.
     *
     * This can be path URL or $_FILES.
     *
     * @var mixed
     */
    protected $file;

    /**
     * Original file uploaded result or open file.
     *
     * @var array
     */
    protected $master;

    /**
     * Result last file uploaded.
     *
     * @var array
     */
    protected $result = array();

    /**
     * Result of all file uplaoded include resized.
     *
     * @var array
     */
    protected $results = array();

    /**
     * Create Uploader instance.
     *
     * @param Repository $config
     * @param Request    $request
     * @param Filesystem $files
     */
    public function __construct(array $config, Request $request, Filesystem $files)
    {
        // Get config from file.
        $this->config = $config;

        // Laravel request.
        $this->request = $request;

        // Laravel filesystem.
        $this->files = $files;
    }

    /**
     * Inject config.
     *
     * @param   array  $params
     * @return  Attach
     */
    public function inject($config = array())
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }

        return $this;
    }

    /**
     * Add file to process.
     *
     * Input can be string URL or $_FILES
     *
     * @param   mixed  $file
     * @return  Attach
     */
    public function add($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Hashed file name generate.
     *
     * Generate a uniqe name to be file name.
     *
     * @param   string  $file_name
     * @return  string
     */
    protected function name($filename)
    {
        // Get extension.
        $extension = $this->files->extension($filename);

        return md5(Str::random(30).time()).'.'.$extension;
    }

    /**
     * Find a base directory include appended.
     *
     * Destination dir to upload.
     *
     * @param   string  $base
     * @return  string
     */
    protected function path($base = null)
    {
        $path = $this->config['subpath'];

        // Path config can be closure.
        if ($path instanceof Closure) {
            return $path() ? $base.'/'.$path().'/' : $base.'/';
        }

        return $path ? $base.'/'.$path.'/' : $base.'/';
    }

    /**
     * Add a new result uplaoded.
     *
     * @return void
     */
    protected function addResult($result)
    {
        // Fire a result to callback.
        $onUpload = $this->config['onUpload'];

        if ($onUpload instanceof Closure) {
            $onUpload($result);
        }

        $this->results[$result['scale']] = $result;
    }

    /**
     * Reset after uploaded master.
     *
     * @return void
     */
    protected function reset()
    {
        $this->file = null;
    }

    /**
     * Return all process results to callback.
     *
     * @return mixed
     */
    public function onComplete($closure = null)
    {
        return ($closure instanceof Closure) ? $closure($this->results) : $this->results;
    }

    /**
     * After end of all process fire results to callback.
     *
     * @return void
     */
    public function __destruct()
    {
        $onComplete = $this->config['onComplete'];
        if ($onComplete instanceof Closure) {
            $onComplete($this->results);
        }
    }

}
