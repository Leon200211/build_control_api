<?php


namespace engine\main\authentication\models;

use engine\base\controllers\Singleton;
use engine\base\exceptions\AuthException;
use engine\base\models\BaseModel;


// основная модель при входе
class MainModel extends BaseModel
{

    // трейт для паттерна Singleton
    use Singleton;


    public function checkAuthentication(Array $loginData): Array
    {
        // ищем пользователя
        if (!$this->_userExists($loginData['username'])) {
            throw new AuthException('Error');
        }

        $userData = $this->read('users', [
            'fields' => ['id', 'name', 'password'],
            'where' => ['login' => $loginData['username']]
        ])[0];

        if ($userData['password'] === $loginData['password']) {
            return $userData;
        } else {
            // попытка подбора пароля
            throw new AuthException('Error2');
        }

    }

    private function _userExists(String $userName): bool
    {
        $userId = $this->read('users', [
            'fields' => ['id'],
            'where' => ['login' => $userName]
        ]);

        if (!empty($userId)) {
            return true;
        }

        return false;
    }



}