<?php
/**
 * Created by PhpStorm.
 * User: it-iguxa
 * Date: 2018-08-27
 * Time: 9:42
 */

//парсинк сайта с эмоджи в бд
die();
define('ROOT',dirname(__FILE__));
require_once(ROOT.'/components/telegram_bot.php');
require_once(ROOT.'/components/db.php');
include_once(ROOT.'/lib/simple_html_dom.php');
include_once(ROOT.'/lib/curl_function.php');
include_once(ROOT.'config/db_params.php');
include_once(ROOT.'components/db.php');

$url = 'http://www.forex-money.org/%D1%81%D0%B8%D0%BC%D0%B2%D0%BE%D0%BB%D1%8B-%D0%B2%D0%B0%D0%BB%D1%8E%D1%82-%D1%81%D1%82%D1%80%D0%B0%D0%BD-%D0%BC%D0%B8%D1%80%D0%B0';

$html = curl_function($url);
$emo = str_get_html($html);
//file_put_contents('1.html',$html);
$emojis = $emo->find('.emoji');
$countrys = $emo->find('.td-country');
foreach ($emojis as $emoji1){
    $emoji[] = $emoji1->plaintext;
}

foreach ($countrys as $country1){
    $country[] = $country1->plaintext;
}
$arrays = array_combine($emoji,$country);

foreach ($arrays as $key => $array){
    $data['country'] = $array;
    $data['emoji'] = $key;
    $data['wiki'] = 'https://ru.wikipedia.org/wiki/'.str_replace(' ', '_', $array);
    DB::setEmoji($data);

}
echo '<pre>';
var_dump($array_result);


