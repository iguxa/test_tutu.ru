<?php
class Db
{
    public static function getConnection()
    {
        $paramsPath = ROOT . '/config/db_params.php';
        $params = include($paramsPath);
        $dsn = 'mysql:dbname='.$params['dbname'].';host='.$params['host'];
        $db = new PDO($dsn,$params['user'],$params['password'],$params['options']);
        return $db;
    }
    public static function getPdo($sql)
    {
        $db = self::getConnection();
        $result = $db -> prepare($sql);
        $result -> execute();
        return $result;
    }
    //добавление эмоджи в таблицу
    public static function setEmoji($data = [])
    {
        $db = self::getConnection();
        $sql = "INSERT INTO `emoji` SET `country` = :country,`emoji`=:emoji,`wiki`=:wiki";
        $result = $db -> prepare($sql);
        $result -> execute(array(':country' => $data['country'],':emoji' => $data['emoji'],':wiki' => $data['wiki']));
        //$result -> execute(array($id));
        return $result;
    }
    //получение эмоджи
    public static function getEmoji($country)
    {
        $db = self::getConnection();
        $sql = "SELECT `country`,`emoji`,`wiki`
                FROM `emoji`
                WHERE MATCH (`emoji`.`country`) AGAINST (:country IN BOOLEAN MODE ) LIMIT 1";
        $result = $db -> prepare($sql);
        $result -> execute(array(':country' => $country));
        return $result->fetch();
    }
}