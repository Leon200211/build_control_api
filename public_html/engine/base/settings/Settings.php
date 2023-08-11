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
        ],

        '/api/profile/my' => [
            'controller' => 'Profile',
            'controllerPath' => '\engine\main\profile\controllers\\',
            'action' => 'getMyProfile',
        ],


        '/api/projects' => [
            'controller' => 'Projects',
            'controllerPath' => '\engine\modules\residential_complexes\projects\controllers\\',
            'action' => 'getProjects',
        ],

        '/api/houses' => [
            'controller' => 'Houses',
            'controllerPath' => '\engine\modules\residential_complexes\houses\controllers\\',
            'action' => 'getHouses',
        ],
        '/api/house' => [
            'controller' => 'House',
            'controllerPath' => '\engine\modules\residential_complexes\houses\controllers\\',
            'action' => 'getHouse',
        ],

        '/api/section' => [
            'controller' => 'Section',
            'controllerPath' => '\engine\modules\residential_complexes\sections\controllers\\',
            'action' => 'getSection',
        ],

        '/api/floor' => [
            'controller' => 'Floor',
            'controllerPath' => '\engine\modules\residential_complexes\floors\controllers\\',
            'action' => 'getFloor',
        ]

    ];


}