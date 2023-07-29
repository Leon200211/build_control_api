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


    /**
     * Проверка данных пользователя
     * @param array $loginData
     * @return Array
     * @throws AuthException
     */
    public function checkAuthentication(Array $loginData): Array
    {
        // ищем пользователя
        if (!$this->_userExists($loginData['username'])) {
            throw new AuthException('Неверный логин или пароль');
        }

        $userData = $this->read('users', [
            'fields' => ['id', 'name', 'password'],
            'where' => ['login' => $loginData['username']]
        ])[0];

        if ($userData['password'] === $loginData['password']) {
            return $userData;
        } else {
            // попытка подбора пароля
            throw new AuthException('Неверный логин или пароль');
        }

    }


    /**
     * Проверка на существование пользователя
     * @param String $userName
     * @return bool
     */
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


    /**
     * Метод по проверке и добавлению записи в refreshSessions
     * @param array $userData
     * @param array $tokens
     * @return void
     */
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


    /**
     * Метод по созданию записи в refreshSessions
     * @param array $userData
     * @param array $tokens
     * @return void
     */
    private function _createRefreshSession(Array $userData, Array $tokens): void
    {
        $this->add('refreshSessions', [
            'fields' => [
                'userId' => $userData['id'],
                'refreshToken' => $tokens['refresh_token'],
                'expiresIn' => $tokens['rt_expires_in'],
                'fingerprint' => $userData['id']
            ]
        ]);
    }


    /**
     * Метод для удаления всех RefreshSession определенного пользователя
     * @param int $userId
     * @return void
     */
    private function _destroyRefreshSession(int $userId): void
    {
        $this->delete('refreshSessions', [
            'where' => ['userId' => $userId]
        ]);
    }


    /**
     * Проверка RefreshSession у токена
     * @param array $data
     * @return bool
     * @throws AuthException
     */
    public function checkRefreshSession(Array $data): bool
    {
        $refreshSession = $this->read('refreshSessions', [
            'fields' => ['id', 'expiresIn'],
            'where' => [
                'userId' => $data['user_id'],
                'refreshToken' => $data['refresh_token'],
                'fingerprint' => $data['user_id']
            ]
        ]);

        if (empty($refreshSession)) {
            throw new AuthException('INVALID_REFRESH_SESSION');
        }

        $this->_deleteRefreshSession($refreshSession[0]['id']);

        if ($refreshSession[0]['expiresIn'] < time()) {
            throw new AuthException('TOKEN_EXPIRED');
        }

        return true;
    }


    /**
     * Метод для удаления Refresh токена
     * @param array $data
     * @return bool
     * @throws AuthException
     */
    public function deleteRefreshSession(Array $data): bool
    {
        $refreshSession = $this->read('refreshSessions', [
            'fields' => ['id', 'expiresIn'],
            'where' => [
                'userId' => $data['user_id'],
                'refreshToken' => $data['refresh_token'],
                'fingerprint' => $data['user_id']
            ]
        ]);

        if (empty($refreshSession)) {
            throw new AuthException('INVALID_REFRESH_SESSION');
        }

        $this->_deleteRefreshSession($refreshSession[0]['id']);

        return true;
    }


    /**
     * Метод для удаления RefreshSession
     * @param int $id
     * @return void
     */
    private function _deleteRefreshSession(int $id): void
    {
        $this->delete('refreshSessions', [
            'where' => ['id' => $id]
        ]);
    }


    /**
     * Метод для получения данных пользователя
     * @param int $userId
     * @return mixed
     * @throws AuthException
     */
    public function getUserData(int $userId): Array
    {
        $userData = $this->read('users', [
            'fields' => ['id', 'name', 'password'],
            'where' => ['id' => $userId]
        ]);

        if (!empty($userData)) {
            return $userData[0];
        } else {
            throw new AuthException('Ошибка авторизации');
        }
    }



}