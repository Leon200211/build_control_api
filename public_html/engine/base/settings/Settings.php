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
            'controller' => 'main',
            'controllerPath' => '\engine\main\authentication\controllers\\',
            'action' => 'index',
        ],

    ];


}