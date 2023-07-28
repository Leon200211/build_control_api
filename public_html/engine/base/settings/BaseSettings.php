<?php

namespace engine\base\settings;

use engine\base\controllers\Singleton;

/**
 * трейт для работы с другими классами настроек
 */
trait BaseSettings
{
    private $_baseSettings;  // для доступа к экземпляру Settings

    use Singleton {
        getInstance as SingletonInstance;
    }

    static public function getInstance(): self
    {
        if (self::$_instance instanceof self) {  // проверка существует ли уже объект класса
            return self::$_instance;
        }

        // для доступа к экземпляру Settings
        self::SingletonInstance()->_baseSettings = Settings::getInstance();
        // для склейки полей
        $baseProperties = self::$_instance->_baseSettings->clueProperties(get_class());
        // для записи значений склейки
        self::$_instance->setProperty($baseProperties);

        return self::$_instance;  // если еще нет объекта, создать
    }

    // геттер для получения данных
    static public function get($property)
    {
        return self::getInstance()->$property;
    }

    // для записи значений после склейки
    protected function setProperty($properties): void
    {
        if ($properties) {
            foreach ($properties as $name => $property){
                $this->$name = $property;
            }
        }
    }

}