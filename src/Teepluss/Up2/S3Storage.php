<?php namespace Teepluss\Up2;

use Closure;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v2\AwsS3Adapter;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem as S3Filesystem;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;


class S3Storage extends StoreAbstract implements StoreInterface {

    protected $client;

    protected $filesystem;

    /**
     * Create Uploader instance.
     *
     * @param Repository $config
     * @param Request    $request
     * @param Filesystem $files
     */
    public function __construct(array $config, Request $request, Filesystem $files)
    {
        parent::__construct($config, $request, $files);

        $this->client = S3Client::factory(array(
            'key'    => $config['key'],
            'secret' => $config['secret'],
            'region' => $config['region']
        ));

        $this->filesystem = new S3Filesystem(new AwsS3Adapter($this->client, $config['bucket'], null));
    }

    /**
     * Open the location path.
     *
     * $name don't need to include path.
     *
     * @param   string  $name
     * @return  Attach
     */
    public function open($node)
    {
        $location = $node['location'];

        // Generate a result to use as a master file.
        $result = $this->results($location);

        $this->master = $result;

        return $this;
    }

    /**
     * Request object url.
     *
     * @param  string $path
     * @return string
     */
    public function requestUrl($path)
    {
        return $this->filesystem->getAdapter()->getClient()->getObjectUrl($this->config['bucket'], $path);
    }

    /**
     * Generate a view link.
     *
     * @param   string  $path
     * @return  string
     */
    public function url($path)
    {
        $protocol = $this->request->secure() ? 'https://' : 'http://';

        return "{$protocol}{$this->config['bucket']}.s3-{$this->config['region']}.amazonaws.com{$path}";
    }

    /**
     * Uplaod a file to destination.
     *
     * @return Attach
     */
    public function upload()
    {
        // Find a base directory include appended.
        $path = $this->path();

        // Method to upload.
        $method = 'doUpload';

        switch ($this->config['type'])
        {
            case 'base64' : $method = 'doBase64'; break;
            case 'remote' : $method = 'doTransfer'; break;
            case 'detect' :

                if (preg_match('|^http(s)?|', $this->file))
                {
                    $method = 'doTransfer';
                }
                elseif (preg_match('|^data:|', $this->file))
                {
                    $method = 'doBase64';
                }

                break;
        }

        // Call a method.
        $result = call_user_func_array(array($this, $method), array($this->file, $path));

        // If uploaded set a master add fire a result.
        if ($result !== false)
        {
            $this->master = $result;
            $this->addResult($result);
        }

        // Reset values.
        $this->reset();

        return $this;
    }

    /**
     * Upload from a file input.
     *
     * @param   SplFileInfo  $file
     * @param   string       $path
     * @return  mixed
     */
    public function doUpload($file, $path)
    {
        if ( ! $file instanceof \SplFileInfo)
        {
            $file = $this->request->file($file);
        }

        // Original name.
        $origName = $file->getClientOriginalName();

        // Extension.
        $extension = $file->getClientOriginalExtension();

        // Generate a file name with extension.
        $fileName = $this->name($origName);

        // Use Imagine to reduce size and quality depend on config.
        $options = array(
            'jpeg_quality'          => array_get($this->config, 'quality.jpeg', 90),
            'png_compression_level' => array_get($this->config, 'quality.png', 90) / 10,
        );

        $imagine = new Imagine();
        $image = $imagine->open($file);

        $image->interlace(ImageInterface::INTERLACE_PLANE);

        $uploadPath = $path.$fileName;

        $content = $image->get($extension, $options);

        if ($this->filesystem->put($uploadPath, $content))
        {
            return $this->results($uploadPath);
        }

        return false;
    }

    /**
     * Upload from a remote URL.
     *
     * @param   string  $file
     * @param   string  $path
     * @return  mixed
     */
    public function doTransfer($url, $path)
    {
        // Original name.
        $origName = basename($url);

        // Strip query string by buagern.
        $origName = preg_replace('/\?.*/', '', $origName);

        // Generate a file name with extension.
        // $filename = $this->name($url);
        // Fixed by buagern
        $filename = $this->name($origName);

        // Get file binary.
        $ch = curl_init();

        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,120);
        curl_setopt ($ch, CURLOPT_TIMEOUT,120);

        // Response returned.
        $content = curl_exec($ch);

        curl_close($ch);

        // Path to write file.
        $uploadPath = $path.$filename;

        if ($this->filesystem->put($uploadPath, $content))
        {
            return $this->results($uploadPath);
        }

        return false;
    }

    /**
     * Upload from base64 image.
     *
     * @param  string $base64
     * @param  string $path
     * @return mixed
     */
    public function doBase64($base64, $path)
    {
        $base64 = trim($base64);

        // Check pattern.
        if (preg_match('|^data:image\/(.*?);base64\,(.*)|', $base64, $matches))
        {
            $content = base64_decode($matches[2]);

            $extension = $matches[1];

            $origName = 'base64-'.time().'.'.$extension;

            $filename = $this->name($origName);

            // Path to write file.
            $uploadPath = $path.$filename;

            if ($this->filesystem->put($uploadPath, $content))
            {
                return $this->results($uploadPath);
            }
        }

        return false;
    }

    /**
     * Generate file result format.
     *
     * @param   string  $location
     * @param   string  $scale
     * @return  array
     */
    public function results($location, $scale = null)
    {
        // Scale of original file.
        if (is_null($scale))
        {
            $scale = 'original';
        }

        // Is this image?
        $isImage = false;

        // Get pathinfo.
        try
        {
            $metadata = $this->filesystem->getMetadata($location);
        }
        catch (FileNotFoundException $e)
        {
            return false;
        }

        // Append path without base.
        $path = $this->path();

        $fileSize = $metadata['size'];

        // Get an file extension.
        $split = explode('.', $location);
        $fileExtension = end($split);

        // Base name include extension.
        $split = explode('/', $location);
        $fileBaseName = end($split);

        // File name without extension.
        $fileName = str_replace('.'.$fileExtension, '', $fileBaseName);

        // Append path with file name.
        $filePath = $path.$fileBaseName;

        // Get mime type.
        $mime  = $metadata['mimetype'];

        if (preg_match('/image/', $mime))
        {
            $isImage = true;
        }

        // Dimension for image.
        $dimension = null;

        // Master of resized file.
        $master = null;

        if ($scale !== 'original')
        {
            $master = str_replace('_'.$scale, '', $fileName);
        }

        return array(
            'isImage'       => $isImage,
            'scale'         => $scale,
            'master'        => $master,
            'subpath'       => $path,
            'location'      => $location,
            'fileName'      => $fileName,
            'fileExtension' => $fileExtension,
            'fileBaseName'  => $fileBaseName,
            'filePath'      => $filePath,
            'fileSize'      => $fileSize,
            'url'           => $this->url($filePath),
            'mime'          => $mime,
            'dimension'     => $dimension
        );
    }

    /**
     * Resize master image file.
     *
     * @param   array   $sizes
     * @return  Attach
     */
    public function resize($sizes = null)
    {
        // A master file to resize.
        $master = $this->master;


        // Master image valid.
        if ( ! is_null($master) and preg_match('|image|', $master['mime']))
        {
            $imageUrl = $this->requestUrl($master['location']);

            if ( ! $imageUrl)
            {
                return false;
            }

            $imagine = new Imagine();
            $image = $imagine->open($imageUrl);

            // Path with base dir.
            $path = $this->path();

            // All scales available.
            $scales = $this->config['scales'];

            // If empty mean generate all sizes from config.
            if (empty($sizes))
            {
                $sizes = array_keys($scales);
            }

            // If string mean generate one size only.
            if (is_string($sizes))
            {
                $sizes = (array) $sizes;
            }

            if (count($sizes)) foreach ($sizes as $size)
            {
                // Scale is not in config.
                if ( ! array_key_exists($size, $scales)) continue;

                // Get width and height.
                list($w, $h) = $scales[$size];

                // Path with the name include scale and extension.
                $uploadPath = $path.$master['fileName'].'_'.$size.'.'.$master['fileExtension'];

                // Use Imagine to make resize and crop.
                $options = array(
                    'jpeg_quality'          => array_get($this->config, 'quality.jpeg', 90),
                    'png_compression_level' => array_get($this->config, 'quality.png', 90) / 10,
                );

                $content = $image->thumbnail(new Box($w, $h), 'outbound')
                                 ->interlace(ImageInterface::INTERLACE_PLANE)
                                 ->get($master['fileExtension'], $options);


                $this->filesystem->put($uploadPath, $content);

                // Add a result and fired.
                $result = $this->results($uploadPath, $size);

                // Add a result.
                $this->addResult($result);
            }
        }

        return $this;
    }

    /**
     * Remove master image file.
     *
     * @return  Attach
     */
    public function remove()
    {
        $master = $this->master;

        $stacks = array();

        if ( ! is_null($master))
        {
            $location = $master['location'];

            try
            {
                if ($this->filesystem->has($location))
                {
                    $this->filesystem->delete($location);
                }
            }
            catch (FileNotFoundException $e)
            {
                // File not found on S3.
            }

            // Fire a result to callback.
            $onRemove = $this->config['onRemove'];

            if ($onRemove instanceof Closure)
            {
                $onRemove($master);
            }
        }

        return $this;
    }

}