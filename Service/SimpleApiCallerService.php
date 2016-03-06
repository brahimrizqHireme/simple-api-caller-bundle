<?php
namespace rubenrubiob\SimpleApiCallerBundle\Service;

use rubenrubiob\SimpleApiCallerBundle\Caller\HttpfulSimpleApiCaller;

/**
 * Class SimpleApiCallerService
 * @package rubenrubiob\SimpleApiCallerBundle\Service
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
}