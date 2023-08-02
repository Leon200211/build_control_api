<?php

namespace engine\main\profile\models;

use engine\base\controllers\Singleton;
use engine\base\exceptions\AuthException;
use engine\base\models\BaseModel;


/**
 * Модель для работы с профилями
 */
class MainModel extends BaseModel
{

    // трейт для паттерна Singleton
    use Singleton;


    /**
     * Метод получения данных пользователя
     * @param $idUser
     * @return Array
     * @throws \Exception
     */
    public function getUserData($idUser): Array
    {
        $userData = $this->read('users', [
            'fields' => ['name', 'position', 'info', 'role', 'phone', 'email'],
            'where' => ['id' => $idUser]
        ]);

        if (empty($userData)) {
            throw new \Exception('Пользователь не найден');
        }

        $result = $userData[0];
        $result['profile_img'] = $this->getUserImg($idUser);

        return $result;
    }

}