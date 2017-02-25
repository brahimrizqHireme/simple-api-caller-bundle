<?php
namespace rubenrubiob\SimpleApiCallerBundle\Service;

use rubenrubiob\SimpleApiCallerBundle\Caller\HttpfulSimpleApiCaller;

/**
 * Class SimpleApiCallerService
 * @package rubenrubiob\SimpleApiCallerBundle\Service
 *
 * @method array get($url, $headers)
 * @method array post($url, $data, $headers)
 * @method array put($url, $data, $headers)
 * @method array patch($url, $data, $headers)
 */
class SimpleApiCallerService
{
    /**
     * @var HttpfulSimpleApiCaller
     */
    protected $apiCaller;

    /**
     * ApiCallerService constructor.
     * @param string $cacheDir
     */
    public function __construct($cacheDir = '')
    {
        $this->apiCaller = HttpfulSimpleApiCaller::getInstance($cacheDir);
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // Magically call the required function
        call_user_func_array(array($this->apiCaller, $name), $arguments);

        return $this->apiCaller->getData();
    }

    /**
     * @param string $mimeType
     * @return $this
     */
    public function expects($mimeType)
    {
        $this->apiCaller->setMimeType($mimeType);

        return $this;
    }
}
