<?php
namespace rubenrubiob\SimpleApiCallerBundle\Caller;

/**
 * Interface SimpleApiCallerInterface
 * @package rubenrubiob\SimpleApiCallerBundle\Caller
 */
interface SimpleApiCallerInterface
{
    /**
     * @param string $url
     * @param array  $headers
     * @return mixed
     */
    public function get($url = '', $headers = array());

    /**
     * @param string $url
     * @param array  $data
     * @param array  $headers
     * @return mixed
     */
    public function post($url = '', $data = array(), $headers = array());

    /**
     * @param array $headers
     * @return mixed
     */
    public function setHeaders($headers = array());

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param string $cacheDir
     * @return mixed
     */
    public static function getInstance($cacheDir = '');
}