<?php
/**
 * File: Curl.php
 * PROJECT_NAME: php-solution
 */

/**
 * Class Curl
 * @link https://www.php.net/manual/zh/function.curl-setopt.php
 */
class Curl
{
    public static function simpleGet(string $url)
    {
        $ch = curl_init();
        if (false !== stripos($url, 'https://')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }


}