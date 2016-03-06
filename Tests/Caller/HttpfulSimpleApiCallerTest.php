<?php

namespace rubenrubiob\SimpleApiCallerBundle\Tests\Caller;

use rubenrubiob\SimpleApiCallerBundle\Caller\HttpfulSimpleApiCaller;

/**
 * Class HttpfulSimpleApiCallerTest
 * @package rubenrubiob\SimpleApiCallerBundle\Tests\Caller
 */
class HttpfulSimpleApiCallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getInstance method
     */
    public function testGetInstance()
    {
        $apiCaller = HttpfulSimpleApiCaller::getInstance();

        $this->assertEquals(true, $apiCaller instanceof HttpfulSimpleApiCaller);
        $this->assertEquals(
            true,
            in_array('rubenrubiob\SimpleApiCallerBundle\Caller\SimpleApiCallerInterface', class_implements($apiCaller))
        );
        $this->assertEquals($apiCaller, HttpfulSimpleApiCaller::getInstance());
    }

    /**
     * Test getData method
     */
    public function testGetData()
    {
        $apiCaller = HttpfulSimpleApiCaller::getInstance();

        $this->assertEquals(array(), $apiCaller->getData());
    }

    /**
     * Test getData method
     */
    public function testHeaders()
    {
        $apiCaller = HttpfulSimpleApiCaller::getInstance();

        $apiCaller->setHeaders();
    }
}
