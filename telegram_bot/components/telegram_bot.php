<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.03.2018
 * Time: 14:22
 */
include_once (ROOT.'/config/telegram_config.php');
//праметры
class param{
    static function take_param(){
        $paramsPath = 'config/telegram_config.php';
        $params = include($paramsPath);
        return $params;
    }
    static function token(){
        $params = self::take_param();
        return $params['token'];
    }
    static function chat_id_chanel(){
        $params = self::take_param();
        return $params['chat_id_chanel'];
    }
}
//получение эмоджи
class Emoji{
    public $emoji;//страна,эмоджи,вики
    public $country;//страна

    public function __construct($country)
    {
        $this->country = $country;
        $search = '*'.$country.'*';
        self::getEmoji($search);//получение эмоджи по стране
    }
    //получение эмоджи по стране
    private function getEmoji($country)
    {
        $result = Db::getEmoji($country);
        $this->emoji = $result;
    }
    //response
    public function getAnswer()
    {
        $emoji = $this->emoji;
        if($emoji){
            return "<b>".$emoji['country']."</b> ".$emoji['emoji']."\r\n".$emoji['wiki'];
        }
        else{
            return "Такой страны не найдено,возможно ошибка в названии - <b>$this->country</b>";
        }
    }
}

