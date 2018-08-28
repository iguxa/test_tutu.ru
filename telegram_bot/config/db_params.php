<?php

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
);


return array(
    'host' => 'localhost',
    'dbname' => 'dbname',
    'user' => 'user',
    'password' => 'password&9',
    'options' => $options
);