<?php
namespace rubenrubiob\SimpleApiCallerBundle\Caller;

use Httpful\Httpful;
use Httpful\Mime;
use Httpful\Request;
use Httpful\Handlers\JsonHandler;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class HttpfulApiCaller
 * @package rubenrubiob\ApiCallerBundle\Caller
 */
class HttpfulSimpleApiCaller implements SimpleApiCallerInterface
{
    /**
     * @var HttpfulSimpleApiCaller
     */
    protected static $instance;

    /**
     * @var string
     */
    private static $cacheDir;

    /**
     * @var mixed
     */
    protected $data = null;

    /**
     * @var null|array
     */
    protected $headers;

    /**
     * @param string $url
     * @param array  $headers
     * @return mixed
     */
    public function get($url = '', $headers = array())
    {
        // Set headers
        $this->setHeaders($headers);
        // Set template
        $this->setTemplate();

        // Perform the request
        $this->data = Request::get($url)->send();
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     * @return mixed
     */
    public function post($url = '', $data = array(), $headers = array())
    {
        // Set headers
        $this->setHeaders($headers);
        // Set template
        $this->setTemplate();

        $postData = array();
        $files = array();
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                if ($value instanceof UploadedFile) {
                    $files[$key] = $this->saveTemporaryFile($value);
                } else {
                    $postData[$key] = $value;
                }
            }
        }

        // Prepare the request
        $request = Request::post($url);

        // Set fields data
        if (is_array($postData) && !empty($postData)) {
            $request->body($postData);
        }

        // Set files data
        if (is_array($files) && !empty($files)) {
            $request->attach($files);
        }

        // Perform the request
        $this->data = $request->send();

        // Remove temporary files
        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                $this->removeTemporaryFile($file);
            }
        }
    }

    /**
     * @param array $headers
     * @return mixed
     */
    public function setHeaders($headers = array())
    {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        if ($this->data !== null && property_exists($this->data, 'body')) {
            // Return the body of the response, i.e., what the API sends as a response.
            return $this->data->body;
        }

        // Return empty array otherwise
        return array();
    }

    /**
     * @param string $cacheDir
     * @return HttpfulSimpleApiCaller
     */
    public static function getInstance($cacheDir = '')
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        self::$cacheDir = $cacheDir;

        return self::$instance;
    }

    /**
     * Method to set template of request
     */
    private function setTemplate()
    {
        // Register a new mime type handler, so the response is decoded as an array.
        // Extracted from: http://stackoverflow.com/a/22597037
        $jsonHandler = new JsonHandler(array('decode_as_array' => true));
        Httpful::register('application/json', $jsonHandler);

        // Create template
        $template = Request::init()
            ->expectsJson()
            ->sendsType(Mime::FORM);
        ;

        // Add custom headers to request
        if ($this->headers !== null && is_array($this->headers) && !empty($this->headers)) {
            $template->addHeaders($this->headers);
        }

        // Set the template
        Request::ini($template);
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function saveTemporaryFile(UploadedFile $file)
    {
        $originalName = $file->getClientOriginalName();

        // Move uploaded file to temporary path
        $file->move(self::$cacheDir, $file->getClientOriginalName());

        // Return new location
        return sprintf('%s/%s', self::$cacheDir, $originalName);
    }

    /**
     * Method to remove temporary files
     *
     * @param $path
     */
    private function removeTemporaryFile($path)
    {
        unlink($path);
    }
}