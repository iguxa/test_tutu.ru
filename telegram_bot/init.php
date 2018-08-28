<?php
/**
 *
 */
header('Content-Type: text/html; charset=UTF-8');
// подрубаем API
require_once("vendor/autoload.php");

define('ROOT',dirname(__FILE__));
require_once(ROOT.'/components/telegram_bot.php');
require_once(ROOT.'/components/db.php');

// дебаг
if(true){
	error_reporting(E_ALL & ~(E_NOTICE | E_USER_NOTICE | E_DEPRECATED));
	ini_set('display_errors', 1);
}

// создаем переменную бота
$token = param::token();
//$chat_id_chanel = param::chat_id_chanel();
$bot = new \TelegramBot\Api\Client($token,null);


// если бот еще не зарегистрирован - регистируем
if(!file_exists("registered.trigger")){
	/**
	 * файл registered.trigger будет создаваться после регистрации бота.
	 * если этого файла нет значит бот не зарегистрирован
	 */

	// URl текущей страницы
	$page_url = "https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	$result = $bot->setWebhook($page_url);
	if($result){
		file_put_contents("registered.trigger",time()); // создаем файл дабы прекратить повторные регистрации
	} else die("ошибка регистрации");
}

// Запуск бота
$bot->command('start', function ($message) use ($bot) {
    $answer = "<b>Праивла использования:</b>\r\n

Просто напишите название страны,напрмер : <b>Непал</b>\r\n

";
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

// помощь
$bot->command('help', function ($message) use ($bot) {
    $answer = "<b>Праивла использования:</b>\r\n

Просто напишите название страны,напрмер : <b>Непал</b>\r\n

";
    $bot->sendMessage($message->getChat()->getId(), $answer);
});


// Отлов любых сообщений + обрабтка reply-кнопок
$bot->on(function($Update) use ($bot){

	$message = $Update->getMessage();
	$mtext = $message->getText();
	$cid = $message->getChat()->getId();

    $answer = new Emoji($mtext);
    $result = $answer->getAnswer();
    $bot->sendMessage($cid, $result);

}, function($message) use ($name){
	return true; // когда тут true - команда проходит
});
// запускаем обработку
$bot->run();

echo "бот";