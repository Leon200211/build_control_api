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


    public function refreshSession(Array $userData, Array $tokens): void
    {
        $refreshSessionsCount = $this->read('refreshSessions', [
            'where' => ['userId' => $userData['id']]
        ]);

        if (!empty($refreshSessionsCount) and count($refreshSessionsCount) >= 5) {
            $this->_destroyRefreshSession($userData['id']);
        }

        $this->_createRefreshSession($userData, $tokens);
    }

    private function _destroyRefreshSession(int $userId): void
    {
        $this->delete('refreshSessions', [
            'where' => ['userId' => $userId]
        ]);
    }

    private function _createRefreshSession(Array $userData, Array $tokens): void
    {
        $this->add('refreshSessions', [
            'fields' => [
                'userId' => $userData['id'],
                'refreshToken' => $tokens['refresh_token'],
                'expiresIn' => $tokens['expires_in'],
                'fingerprint' => $userData['id']
            ]
        ]);
    }


}