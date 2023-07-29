<?php


namespace engine\base\settings;


use engine\base\controllers\Singleton;


/**
 * Class Settings класс настроек
 * @package engine\base\settings
 */
class Settings
{

    use Singleton;

    // геттер для получения данных
    static public function get($property){
        return self::getInstance()->$property;
    }


    // настройки пути
    private $routes = [

        '/' => [
            'controller' => 'Authorization',
            'controllerPath' => '\engine\main\authentication\controllers\\',
            'action' => 'validateToken',
        ],

        '/api/auth/login' => [
            'controller' => 'Authorization',
            'controllerPath' => '\engine\main\authentication\controllers\\',
            'action' => 'login',
        ],

        '/api/auth/refresh-tokens' => [
            'controller' => 'Authorization',
            'controllerPath' => '\engine\main\authentication\controllers\\',
            'action' => 'refreshTokens',
        ],

        '/api/auth/logout' => [
            'controller' => 'Authorization',
            'controllerPath' => '\engine\main\authentication\controllers\\',
            'action' => 'logout',
        ]

    ];


}