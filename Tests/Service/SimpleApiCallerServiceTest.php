<?php

namespace rubenrubiob\SimpleApiCallerBundle\Tests\Service;

use rubenrubiob\SimpleApiCallerBundle\Caller\HttpfulSimpleApiCaller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Httpful\Response;

/**
 * Class SimpleApiCallerServiceTest
 * @package rubenrubiob\SimpleApiCallerBundle\Tests\Service
 */
class SimpleApiCallerServiceTest extends WebTestCase
{
    /**
     * @var string
     */
    private $getTestUrl;

    /**
     * @var string
     */
    private $postTestUrl;

    /**
     * @var string
     */
    private $putTestUrl;

    /**
     * @var string
     */
    private $patchTestUrl;

    /**
     * @var HttpfulSimpleApiCaller
     */
    private $simpleApiCallerService;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        // Create kernel and get the service from it
        $kernel = static::createKernel();
        $kernel->boot();

        $this->simpleApiCallerService = $kernel->getContainer()->get('rrb.simple_api_caller');
        $this->cacheDir = $kernel->getContainer()->getParameter('kernel.cache_dir');
        $this->getTestUrl = 'http://jsonplaceholder.typicode.com/posts';
        $this->postTestUrl = 'http://jsonplaceholder.typicode.com/posts';
        $this->putTestUrl = 'http://jsonplaceholder.typicode.com/posts/1';
        $this->patchTestUrl = 'http://jsonplaceholder.typicode.com/posts/1';
    }

    /**
     * Test get method from service
     */
    public function testGet()
    {
        // Perform the call
        $response = $this->simpleApiCallerService->get($this->getTestUrl);

        // Check values
        $this->assertEquals(true, is_array($response));
        $this->assertEquals(100, count($response));

        foreach ($response as $r) {
            $this->assertEquals(true, array_key_exists('userId', $r));
            $this->assertEquals(true, array_key_exists('id', $r));
            $this->assertEquals(true, array_key_exists('title', $r));
            $this->assertEquals(true, array_key_exists('body', $r));
        }

        $headers = array(
            'test-headers'      => true,
        );
        $response = $this->simpleApiCallerService->get($this->getTestUrl, $headers);

        // Check values
        $this->assertEquals(true, is_array($response));
        $this->assertEquals(100, count($response));

        foreach ($response as $r) {
            $this->assertEquals(true, array_key_exists('userId', $r));
            $this->assertEquals(true, array_key_exists('id', $r));
            $this->assertEquals(true, array_key_exists('title', $r));
            $this->assertEquals(true, array_key_exists('body', $r));
        }
    }

    /**
     * Test post method from service
     */
    public function testPost()
    {
        // Create files to send
        $testFileName = sprintf('%s/test.txt', $this->cacheDir);
        file_put_contents($testFileName, 'foo');

        $testFileName2 = sprintf('%s/test2.txt', $this->cacheDir);
        file_put_contents($testFileName2, 'foo2');

        // Prepare data
        $data = array(
            'title'     => 'foo',
            'body'      => 'bar',
            'userId'    => 1,
            'address'   => array(
                'country'       => 'foo',
                'postal_code'   => 'bar',
                'numbers'       => array(
                    'home'          => '600',
                    'work'          => '700',
                    'file'          => new UploadedFile($testFileName2, 'test2.txt', null, null, null, true),
                ),
            ),
            'file'      => new UploadedFile($testFileName, 'test.txt', null, null, null, true),
        );


        $headers  = array(
            'test-headers'      => true,
        );

        // Perform the call
        $response = $this->simpleApiCallerService->post($this->postTestUrl, $data, $headers);

        // Test that response is an array
        $this->assertEquals(true, is_array($response));

        // Test data structure
        $this->assertEquals(true, array_key_exists('id', $response));
        $this->assertEquals(101, $response['id']);



        // Test API only returns the id when sending a file, so we perform another request in order to test
        $data = array(
            'title'     => 'foo',
            'body'      => 'bar',
            'userId'    => 1,
        );

        // Perform the call
        $response = $this->simpleApiCallerService->post($this->postTestUrl, $data, $headers);

        // Test that response is an array
        $this->assertEquals(true, is_array($response));

        // Test data structure
        $this->assertEquals(true, array_key_exists('id', $response));
        $this->assertEquals(true, array_key_exists('title', $response));
        $this->assertEquals(true, array_key_exists('body', $response));
        $this->assertEquals(true, array_key_exists('userId', $response));

        // Test data values
        $this->assertEquals(101, $response['id']);
        $this->assertEquals($data['title'], $response['title']);
        $this->assertEquals($data['body'], $response['body']);
        $this->assertEquals($data['userId'], $response['userId']);
    }

    /**
     * Test put method from service
     */
    public function testPut()
    {
        // Create files to send
        $testFileName = sprintf('%s/test.txt', $this->cacheDir);
        file_put_contents($testFileName, 'foo');

        $testFileName2 = sprintf('%s/test2.txt', $this->cacheDir);
        file_put_contents($testFileName2, 'foo2');

        // Prepare data
        $data = array(
            'title'     => 'foo',
            'body'      => 'bar',
            'userId'    => 1,
            'address'   => array(
                'country'       => 'foo',
                'postal_code'   => 'bar',
                'numbers'       => array(
                    'home'          => '600',
                    'work'          => '700',
                    'file'          => new UploadedFile($testFileName2, 'test2.txt', null, null, null, true),
                ),
            ),
            'file'      => new UploadedFile($testFileName, 'test.txt', null, null, null, true),
        );


        $headers  = array(
            'test-headers'      => true,
        );

        // Perform the call
        $response = $this->simpleApiCallerService->put($this->putTestUrl, $data, $headers);

        // Test that response is an array
        $this->assertEquals(true, is_array($response));

        // Test data structure
        $this->assertEquals(true, array_key_exists('id', $response));
        $this->assertEquals(1, $response['id']);
    }

    /**
     * Test patch method from service
     */
    public function testPatch()
    {
        // Create files to send
        $testFileName = sprintf('%s/test.txt', $this->cacheDir);
        file_put_contents($testFileName, 'foo');

        $testFileName2 = sprintf('%s/test2.txt', $this->cacheDir);
        file_put_contents($testFileName2, 'foo2');

        // Prepare data
        $data = array(
            'title'     => 'foo',
            'body'      => 'bar',
            'userId'    => 1,
            'address'   => array(
                'country'       => 'foo',
                'postal_code'   => 'bar',
                'numbers'       => array(
                    'home'          => '600',
                    'work'          => '700',
                    'file'          => new UploadedFile($testFileName2, 'test2.txt', null, null, null, true),
                ),
            ),
            'file'      => new UploadedFile($testFileName, 'test.txt', null, null, null, true),
        );


        $headers  = array(
            'test-headers'      => true,
        );

        // Perform the call
        $response = $this->simpleApiCallerService->patch($this->patchTestUrl, $data, $headers);

        // Test that response is an array
        $this->assertEquals(true, is_array($response));

        // Test data structure
        $this->assertEquals(true, array_key_exists('id', $response));
        $this->assertEquals(1, $response['id']);



        // Test API only returns the id when sending a file, so we perform another request in order to test
        $data = array(
            'title'     => 'foo',
            'body'      => 'bar',
            'userId'    => 1,
        );

        // Perform the call
        $response = $this->simpleApiCallerService->patch($this->patchTestUrl, $data, $headers);

        // Test that response is an array
        $this->assertEquals(true, is_array($response));

        // Test data structure
        $this->assertEquals(true, array_key_exists('id', $response));
        $this->assertEquals(true, array_key_exists('title', $response));
        $this->assertEquals(true, array_key_exists('body', $response));
        $this->assertEquals(true, array_key_exists('userId', $response));

        // Test data values
        $this->assertEquals(1, $response['id']);
        $this->assertEquals($data['title'], $response['title']);
        $this->assertEquals($data['body'], $response['body']);
        $this->assertEquals($data['userId'], $response['userId']);
    }

    /**
     *
     */
    public function testExpects()
    {
        // Perform the call
        $response = $this->simpleApiCallerService->expects('xml')->get('http://www.w3schools.com/xml/note.xml');

        // Check values
        $this->assertEquals('SimpleXMLElement', get_class($response));
    }
}
