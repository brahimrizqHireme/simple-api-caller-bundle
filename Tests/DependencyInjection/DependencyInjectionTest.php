<?php

namespace rubenrubiob\SimpleApiCallerBundle\Tests\DependencyInjection;

use rubenrubiob\SimpleApiCallerBundle\DependencyInjection\Configuration;
use rubenrubiob\SimpleApiCallerBundle\DependencyInjection\rubenrubiobSimpleApiCallerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DependencyInjectionTest
 * @package rubenrubiob\SimpleApiCallerBundle\Tests\DependencyInjection
 */
class DependencyInjectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test extension
     */
    public function testExtension()
    {
        $extension = new rubenrubiobSimpleApiCallerExtension();
        $extension->load(array(), new ContainerBuilder());
    }

    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $configuration = new Configuration();
        $configuration->getConfigTreeBuilder();
    }
}
