<?php


namespace mazahaler\ProjectConnectionChecker;

/**
 * Class Utils
 * @package mazahaler\ProjectConnectionChecker
 */
class Utils
{
    /**
     * @param $needle
     * @param $haystack
     * @param string $i
     * @return bool
     */
    public static function strContains($needle, $haystack, $i = 'i'): bool
    {
        if (preg_match("#{$needle}#{$i}", $haystack)) {
            return true;
        }
        return false;
    }

    /**
     * @param $string
     * @param string $start
     * @param string $end
     * @return false|string
     */
    public static function getBetween($string, $start = "", $end = "")
    {
        if (strpos($string, $start)) {
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return substr($firstSubStr, 0, $endCharCount);
        } else {
            return '';
        }
    }
}