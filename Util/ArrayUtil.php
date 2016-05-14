<?php
namespace rubenrubiob\SimpleApiCallerBundle\Util;

/**
 * Class ArrayUtil
 * @package rubenrubiob\SimpleApiCallerBundle\Util
 */
class ArrayUtil
{
    /**
     * Checks if an array is multidimensional
     *
     * @param array $a
     * @return bool
     */
    public static function isMultidimensionalArray(array $a)
    {
        if (count($a) != count($a, COUNT_RECURSIVE)) {
            return true;
        }

        return false;
    }

    /**
     * Flattens a multidimensional array to be of the form key[foo][bar] = bar
     *
     * @param array $a
     * @return array
     */
    public static function flattenMultidimensionalArray(array $a)
    {
        $flattenedArray = array();

        foreach ($a as $key => $mainValue) {
            if (is_array($mainValue)) {
                $val = self::flattenArray($key, $mainValue);
                $flattenedArray = array_merge($flattenedArray, $val);
            } else {
                $flattenedArray[$key] = $mainValue;
            }
        }

        return $flattenedArray;
    }

    /**
     * @param string $key
     * @param array  $a
     * @return array
     */
    private static function flattenArray($key, array $a)
    {
        $flattened = array();
        foreach ($a as $y => $value) {
            $fk = sprintf('%s[%s]', $key, $y);
            if (is_array($value)) {
                $flattened = array_merge($flattened, self::flattenArray($fk, $value));
            } else {
                $flattened[$fk] = $value;
            }
        }

        return $flattened;
    }
}
