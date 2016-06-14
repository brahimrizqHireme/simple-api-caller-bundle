<?php
namespace rubenrubiob\SimpleApiCallerBundle\Caller;

use Httpful\Http;
use Httpful\Httpful;
use Httpful\Mime;
use Httpful\Request;
use Httpful\Handlers\JsonHandler;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use rubenrubiob\SimpleApiCallerBundle\Util\ArrayUtil;

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
     * @var string
     */
    protected $mimeType = 'json';

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
        $this->setTemplate('get');

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
        $this->sendData('post', $url, $data, $headers);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     * @return mixed
     */
    public function put($url = '', $data = array(), $headers = array())
    {
        $this->sendData('put', $url, $data, $headers);
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     * @return mixed
     */
    public function patch($url = '', $data = array(), $headers = array())
    {
        $this->sendData('patch', $url, $data, $headers);
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
     * @param string $mimeType
     * @return mixed
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
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
     * Note: If data is a multidimensional array, we have to flatten it before posting it, i.e., an array like:
     *
     * [
     *      key1: value1
     *      key2: [
     *          key2.1: value2.1
     *          key2.2: value2.2
     *      ]
     *      key3: [
     *          key3.1: [
     *              key3.1.1: value3.1.1
     *              key3.1.2: value3.1.2
     *          ]
     *          key3.2: value3.2
     *      ]
     * ]
     *
     * must become
     *
     * [
     *      key1: value1
     *      key2[key2.1]: value2.1
     *      key2[key2.2]: value2.2
     *      key3[key3.1][key3.1.1]: value3.1.1
     *      key3[key3.1][key3.1.2]: value3.1.2
     *      key3[key3.2]: value3.2
     * ]
     *
     * Otherwise, curl is not able to perform the request and throws an error
     *
     * @param string $method
     * @param string $url
     * @param array  $data
     * @param array  $headers
     * @return mixed
     */
    private function sendData($method, $url, $data, $headers)
    {
        // Set headers
        $this->setHeaders($headers);
        // Set template
        $this->setTemplate($method);

        // Flatten array if it is multidimensional
        if (ArrayUtil::isMultidimensionalArray($data)) {
            $data = ArrayUtil::flattenMultidimensionalArray($data);
        }

        // We have to post files and data separately
        $postData = array();
        $files = array();

        foreach ($data as $key => $value) {
            if ($value instanceof UploadedFile) {
                $files[$key] = $this->saveTemporaryFile($value);
            } else {
                $postData[$key] = $value;
            }
        }

        // Prepare the request
        $request = Request::$method($url);

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
     * Method to set template of request
     */
    private function setTemplate($method)
    {
        // Register a new mime type handler if we expect a Json, so the response is decoded as an array.
        // Extracted from: http://stackoverflow.com/a/22597037
        if ($this->mimeType == 'json') {
            $jsonHandler = new JsonHandler(array('decode_as_array' => true));
            Httpful::register('application/json', $jsonHandler);
        }

        // Create template
        $template = Request::init()
            ->method($this->getMethod($method))
            ->expects($this->mimeType)
            ->sendsType(Mime::FORM)
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

    /**
     * @param string $method
     * @return null|string
     */
    private function getMethod($method)
    {
        $httpMethod = null;

        switch ($method) {
            case 'get':
                $httpMethod = Http::GET;
                break;
            case 'put':
                $httpMethod = Http::PUT;
                break;
            case 'patch':
                $httpMethod = Http::PATCH;
                break;
            case 'post':
            default:
                $httpMethod = Http::POST;

        }

        return $httpMethod;
    }
}
