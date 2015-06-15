<?php
/**
 * Created by PhpStorm.
 * User: Shoom
 * Date: 13.06.15
 * Time: 20:34
 */

include_once('Config.php');
include_once('server/idiorm.php');

/**
 * Вспомогательные функции
 */
class Util {
    /**
     * Подключение к БД
     */
    public static function DBConnect(){
        $cfg = new Config();
        ORM::configure('mysql:host='.$cfg->host.';dbname='.$cfg->database.';charset=utf8');
        ORM::configure('username', $cfg->user);
        ORM::configure('password', $cfg->password);
        return 1;
    }
} 