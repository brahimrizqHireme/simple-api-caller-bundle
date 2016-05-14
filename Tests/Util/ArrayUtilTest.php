<?php
namespace rubenrubiob\SimpleApiCallerBundle\Tests\Util;

use rubenrubiob\SimpleApiCallerBundle\Util\ArrayUtil;

/**
 * Class ArrayUtilTest
 * @package rubenrubiob\SimpleApiCallerBundle\Tests\Util
 */
class ArrayUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test isMultidimensionalArray method
     */
    public function testIsMultidimensionalArray()
    {
        $emptyArray = array();

        $notMultidimensionalArray = array(
            'foo',
            'bar',
            'var',
        );

        $multidimensionalArray = array(
            'foo',
            'bar' => array(
                'var',
            ),
        );

        $this->assertEquals(false, ArrayUtil::isMultidimensionalArray($emptyArray));
        $this->assertEquals(false, ArrayUtil::isMultidimensionalArray($notMultidimensionalArray));
        $this->assertEquals(true, ArrayUtil::isMultidimensionalArray($multidimensionalArray));
    }

    /**
     * Test flattenMultidimensionalArray method
     */
    public function testFlattenMultidimensionalArray()
    {
        $emptyArray = array();

        $notMultidimensionalArray = array(
            'foo',
            'bar',
            'var',
        );

        $multidimensionalArray = array(
            'key1'  => 'value1',
            'key2'  => array(
                'key2.1'    => 'value2.1',
                'key2.2'    => 'value2.2',
            ),
            'key3'  => array(
                'key3.1'    => 'value3.1',
                'key3.2'    => array(
                    'key3.2.1'      => 'value3.2.1',
                    'key3.2.2'      => 'value3.2.2',
                    'key3.2.3'      => 'value3.2.3',
                ),
                'key3.3'  => array(
                    'key3.3.1'      => 'value3.3.1',
                    'key3.3.2'      => 'value3.3.2',
                ),
            ),
        );

        $flattenedMultidimensionalArray = array(
            'key1'                      => 'value1',
            'key2[key2.1]'              => 'value2.1',
            'key2[key2.2]'              => 'value2.2',
            'key3[key3.1]'              => 'value3.1',
            'key3[key3.2][key3.2.1]'    => 'value3.2.1',
            'key3[key3.2][key3.2.2]'    => 'value3.2.2',
            'key3[key3.2][key3.2.3]'    => 'value3.2.3',
            'key3[key3.3][key3.3.1]'    => 'value3.3.1',
            'key3[key3.3][key3.3.2]'    => 'value3.3.2',
        );

        $this->assertEquals($emptyArray, ArrayUtil::flattenMultidimensionalArray($emptyArray));
        $this->assertEquals(
            $notMultidimensionalArray,
            ArrayUtil::flattenMultidimensionalArray($notMultidimensionalArray)
        );
        $this->assertEquals(
            false,
            $multidimensionalArray == ArrayUtil::flattenMultidimensionalArray($multidimensionalArray)
        );
        $this->assertEquals(
            $flattenedMultidimensionalArray,
            ArrayUtil::flattenMultidimensionalArray($multidimensionalArray)
        );
    }
}
