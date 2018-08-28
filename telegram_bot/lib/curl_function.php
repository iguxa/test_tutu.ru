<?php
/**
 * Created by PhpStorm.
 * User: it-iguxa
 * Date: 2018-08-27
 * Time: 9:44
 */
function curl_function($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}