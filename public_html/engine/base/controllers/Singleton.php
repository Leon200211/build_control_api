<?php


namespace engine\base\controllers;

/**
 * трейт для реализации паттерна проектирования Singleton
 */
trait Singleton
{

    static private $_instance;


    /**
     * @return void
     */
    private function __construct()
    {
    }


    /**
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * @return RouteController|Singleton|\engine\base\settings\Settings
     */
    static public function getInstance()
    {
        // проверка существует ли уже объект
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }

        self::$_instance = new self;

        // проверяем есть ли экземпляра метод connect, если есть, то вызываем его (для работы с моделями)
        if (method_exists(self::$_instance, 'connect')) {
            self::$_instance->connect();
        }

        return self::$_instance;
    }

}