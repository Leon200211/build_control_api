<?php
#/
// все запросы по ссылкам
// навигационные запросы по сайту


// константа безопасности
define('VG_ACCESS', true);
//error_reporting(0);


$headers = [
    "Access-Control-Allow-Origin: *",
    "Access-Control-Allow-Methods: POST, PUT, PATCH, GET, DELETE, OPTIONS",
    "Access-Control-Allow-Headers: *",
    "Content-Type:text/html;charset=utf-8",
];
foreach ($headers as $header) {
    header($header);
}



// отключаем сообщение о предупреждениях
//error_reporting(0);


require_once 'config.php';  // базовые настройки для хостинга
require_once 'engine/base/settings/internal_settings.php';  // фундаментальные настройки сайта

use engine\base\exceptions\RouteException;  // импортируем пространство имен для исключения
use engine\base\exceptions\DbException;  // импортируем пространство имен для исключения БД
use engine\base\controllers\RouteController;


try{
    RouteController::getInstance()->route();
}
catch (RouteException $e){
    exit($e->getMessage());
}
catch (DbException $e){
    exit($e->getMessage());
}
