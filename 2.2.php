<?php
/**
 * Created by PhpStorm.
 * User: it-iguxa
 * Date: 2018-08-27
 * Time: 17:17
 *
 * Насколько я понял,необходимо было абстрактно реализовать систему очередности выполнения cron задач,как бы я возможно
 * это реализовал .Есть какое то количество сайтов/api на их стороне выполняется скрипт с определенной
 * частотой или по условиям.Далее создал БД в которой хранятся url по которому будем отправлять команды,далее обращался
 * бы к ней и доставал url по времени и отправлял команды по ним,далее приходил бы ответ от сайтов/api о состоянии
 * выполнения скрипта,эти два действия не связаны.Так же у нас стоит ограничение на количество попыток отправить
 * команду.Ответ будет приходить на другой скрипт.
 *
 *
 *
 */

class Curl{
    //функция для отправки команды на серевер(вебхук)
    public static function setCurl($cron_url){
        $message = true;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$cron_url );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //$returned = curl_exec($ch);
        //curl_close($ch);
        //return (json_decode($returned));
    }
}
class Db{
    //получение настроек и подключение к бд
    public static function getConnection()
    {
        $paramsPath = ROOT . '/config/db_params.php';
        $params = include($paramsPath);
        $dsn = 'mysql:dbname='.$params['dbname'].';host='.$params['host'];
        $db = new PDO($dsn,$params['user'],$params['password'],$params['options']);
        return $db;
    }
    //получение списка ссылок,при переходе на которые срабатывает скрипт
    public static function getCron()
    {
        $db = self::getConnection();
        $sql = "SELECT id,url FROM cron WHERE `time` <=NOW() AND status IS NULL AND count_try<5 LIMIT 100 ORDER BY id ASC";
        $result = $db -> prepare($sql);
        $result -> execute();
        return $result;
    }
    //увеличение количества попыток при переходе по полученному url
    public static function CountTryCron($cron_id)
    {
        $db = self::getConnection();
        $sql = "UPDATE cron SET count_try = count_try + 1 WHERE id IN $cron_id";
        $result = $db -> exec($sql);
        return $result;
    }
    //обновление статсуа об успешном срабатывании скрипта
    public static function UpdStatusSuccess($cron_upd)
    {
        $db = self::getConnection();
        $sql = "UPDATE cron SET status = 'success' WHERE id IN ($cron_upd)";
        $result = $db -> exec($sql);
        return $result;
    }
    //обновление статсуа о том что скрипт был запущен и команда успешно дошла ,но произошла ошибка в процессе выполнения скрипта на сервере приемщике
    public static function UpdStatusFail($cron_upd)
    {
        $db = self::getConnection();
        $sql = "UPDATE cron SET status = 'fail' WHERE id IN ($cron_upd)";
        $result = $db -> exec($sql);
        return $result;
    }
    //получение ошибок по типам, не получен ответ от сервера куда была отправлена команда и ошибка в процессе выполнения скрипта на сервере приемщике
    public static function getFails()
    {
        $db = self::getConnection();
        $sql = "SELECT id,url,time FROM cron WHERE status = 'fail' ORDER BY id DESC ";
        $sql_not_resp = "SELECT id,url,time FROM cron WHERE status IS NULL AND count_try>=5  ORDER BY id DESC ";
        $result['fail'] = $db -> prepare($sql) -> execute();
        $result['not_respond'] = $db -> prepare($sql_not_resp) -> execute();
        return $result;
    }
}

class Cron{

    private $urls_cron; //список сылок для перехода и выполнения скрипта на сервере приемщике
    private $cron_status; //получение статуса о выполнении скрипта от сервера на который была отправлена команда
    private $fail; //список ошибок по типам, не получен ответ от сервера куда была отправлена команда и ошибка в процессе выполнения скрипта на сервере приемщике


    public function __construct()
    {
        self::setUrls();
        self::setStatus();
        self::setFails();
    }
    //получение списка ссылок,при переходе на которые срабатывает скрипт
    private function setUrls():void
    {
        $cron_url = false;
        $urls = Db::getCron();
        while ($row = $urls -> fetch()) {
            $cron_url[] = $row;
        }
        $this->urls_cron = $cron_url;
    }
    //получение статуса о выполнении скрипта от сервера на который была отправлена команда
    public function setStatus():void
    {
        $crons_status = $_POST['cron_status'] ?? false;
        $this->cron_status = $crons_status;
    }
    //список ошибок по типам, не получен ответ от сервера куда была отправлена команда и ошибка в процессе выполнения скрипта на сервере приемщике
    public function setFails():void
    {
        $fails = Db::getFails();
        $fail = [];
        $not_respond = [];
        while ($row = $fails['fail'] -> fetch()) {
            $fail[] = $row;
        }
        while ($row = $fails['not_respond'] -> fetch()) {
            $not_respond[] = $row;
        }
        $fail_list['fail'] = $fail;
        $fail_list['not_respond'] = $not_respond;
        $this->fail = $fail_list;
    }
    //составление списка id по которым была сделана поптыка на запуск скрипта на стороннем сервере
    protected function getCrone($cron_urls):string
    {
        $id = false;
        foreach ($cron_urls as $cron_url) {
            Curl::setCurl($cron_url);
            $id .= $cron_url['id'].',';
        }
        $cron_id = rtrim($id, ",");
        return $cron_id;
    }
    //получение списка id у которых успешно и не успешно отработали скрипты на сервере куда была отправлена команда
    protected function getStatus($crons_status):array
    {
        $status['id_success'] = $crons_status['success'] ?? false;
        $status['id_fail'] = $crons_status['fail'] ?? false;
        $id_success = false;
        $id_fail = false;
        $cron_id_success = false;
        $cron_id_fail = false;

        if($status['id_success']){
            foreach ($status['id_success'] as $success) {
                $id_success .= $success. ',';
            }
            $cron_id_success = rtrim($id_success['id_success'], ",");
        }
        if($status['id_fail']){
            foreach ($status['id_fail'] as $fail) {
                $id_fail .= $fail. ',';
            }
            $cron_id_fail = rtrim($id_fail['id_fail'], ",");
        }
        $cron_status['id_success'] = $cron_id_success;
        $cron_status['id_fail'] = $cron_id_fail;

        return $cron_status;
    }
    //запуск скрипта
    public function updCrone()
    {
        $cron_urls = $this->urls_cron ?? false;//список url на которые надо отправить команды
        if($cron_urls){
            $cron_id = self::getCrone($cron_urls);//отправка команд
            Db::CountTryCron($cron_id);//увеличение количества попыток отправить команду,при достижении более 5 попыток запустить скрипт,убирается из списка для повторной отправки команды
        }
    }
    //обработка ответа с сервера на который была отправлена команда
    public function updStatus()
    {
        $crons_status = $this->cron_status ?? false;
        if($crons_status){
            $cron_upd = self::getStatus($crons_status);//получение списка id у которых успешно и не успешно отработали скрипты на сервере куда была отправлена команда
            if($crons_status['id_success']){
                Db::UpdStatusSuccess($cron_upd);//обновить статус успешно завершенных скриптов
            }
            if($crons_status['id_fail']){
                Db::UpdStatusFail($cron_upd);//обновить статус скриптов завершенных с ошибкой на сервере исполнителе
            }
        }
    }
    //получение ошибок по типам, не получен ответ от сервера куда была отправлена команда и ошибка в процессе выполнения скрипта на сервере приемщике
    public function Debug(){
        $fail = $this->fail ?? false;;
        return $fail;
    }

}
//Запуск скрипта поставить на крон согласно частоты необходимости выполнения скриптов
$cron = new Cron();
$cron->updCrone();
//Получение ответа от серевера на которые была отправлена команда
$cron = new Cron();
$cron->updStatus();