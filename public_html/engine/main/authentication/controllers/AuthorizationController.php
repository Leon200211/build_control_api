<?php


namespace engine\main\authentication\controllers;


use engine\base\controllers\BaseController;
use engine\base\controllers\Singleton;
use engine\base\exceptions\AuthException;
use engine\main\authentication\libs\php_jwt\ExpiredException;
use engine\main\authentication\libs\php_jwt\JWT;
use engine\main\authentication\libs\php_jwt\Key;
use engine\main\authentication\libs\php_jwt\RT;
use engine\main\authentication\models\MainModel;



/**
 * Class AuthorizationController класс для авторизации
 * @package engine\main\authentication\controllers
 */
class AuthorizationController extends AuthenticationController
{

    private string $_key = '1111';
    private string $_iss = 'http://any-site.org';
    private string $_aud = 'http://any-site.org';
    private int $_iat = 1356999524;
    private int $_nbf = 1357000000;

    private array $_response;


    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->execBase();
    }


    /**
     * @return false|string
     * @throws \engine\base\exceptions\RouteException
     */
    public function outputData(): string
    {
        return json_encode($this->_response);
    }


    /**
     * Метод авторизации
     * @return void
     */
    public function login(): void
    {
        $username = 'root';
        $password = '$2y$10$mvH.8LM6dtENkJSjVidD6ujovmJbmisTL7p38f1MlchUFsIkD1ksy';
        $fingerprint = '';

        $loginData = [
            'username' => $username,
            'password' => $password,
        ];

        try {
            // проверят подлинность логина/пароля
            $userData = $this->model->checkAuthentication($loginData);

            // Генерируем токены
            $tokens = $this->_generateTokens($userData);

            // Проверяем refreshSessions и делаем запись
            $this->model->refreshSession($userData, $tokens);

            $this->_response = $tokens;
        } catch (AuthException $e) {
            $this->_response = [
                'error' => $e
            ];
            //echo $e;
        }
    }


    /**
     * Метод обновления токенов
     * @return void
     */
    public function refreshTokens(): void
    {
        $user_id = '1';
        $refreshToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYW55LXNpdGUub3JnIiwiYXVkIjoiaHR0cDovL2FueS1zaXRlLm9yZyIsImlhdCI6MTM1Njk5OTUyNCwibmJmIjoxMzU3MDAwMDAwLCJleHAiOjE2OTA2NDI2MzksImRhdGEiOnsiaWQiOiIxIiwibmFtZSI6Ilx1MDQxOFx1MDQzMlx1MDQzMFx1MDQzZFx1MDQzZVx1MDQzMiBcdTA0MThcdTA0MzJcdTA0MzBcdTA0M2QgXHUwNDE4XHUwNDMyXHUwNDMwXHUwNDNkXHUwNDNlXHUwNDMyXHUwNDM4XHUwNDQ3In19.-JldpIfgswz4u7ezSMN9Ux0YS132np1rzTsJM4FYnFM';

        $data = [
            'user_id' => $user_id,
            'refresh_token' => $refreshToken
        ];

        try {
            $this->model->checkRefreshSession($data);

            // Получаем данные пользователя из БД
            $userData = $this->model->getUserData($data['user_id']);

            // Генерируем токены
            $tokens = $this->_generateTokens($userData);

            // Проверяем refreshSessions и делаем запись
            $this->model->refreshSession($userData, $tokens);

            $this->_response = $tokens;
        } catch (AuthException $e) {
            $this->_response = [
                'error' => $e
            ];
        }


    }


    /**
     * Метод деавторизации
     * @return void
     */
    public function logout(): void
    {
        $user_id = '1';
        $refreshToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYW55LXNpdGUub3JnIiwiYXVkIjoiaHR0cDovL2FueS1zaXRlLm9yZyIsImlhdCI6MTM1Njk5OTUyNCwibmJmIjoxMzU3MDAwMDAwLCJleHAiOjE2OTA2NDI3MTgsImRhdGEiOnsiaWQiOiIxIiwibmFtZSI6Ilx1MDQxOFx1MDQzMlx1MDQzMFx1MDQzZFx1MDQzZVx1MDQzMiBcdTA0MThcdTA0MzJcdTA0MzBcdTA0M2QgXHUwNDE4XHUwNDMyXHUwNDMwXHUwNDNkXHUwNDNlXHUwNDMyXHUwNDM4XHUwNDQ3In19.kF_-SsJ-Iiq3CBcdS6ZhBtn66poBWp9DHyOufXzj55Y';


        $data = [
            'user_id' => $user_id,
            'refresh_token' => $refreshToken
        ];

        try {
            $this->model->deleteRefreshSession($data);


            $this->_response = ['123421'];

        } catch (AuthException $e) {
            $this->_response = [
                'error' => $e
            ];
        }
    }


    /**
     * Метод для проверки jwt
     * @param string $jwt
     * @return bool
     */
    public function validateToken(string $jwt): bool
    {
        try {
            // Декодирование jwt
            $decoded = JWT::decode($jwt, new Key($this->_key, 'HS256'));

            return true;
        } catch (ExpiredException $e) {
            return false;
        }
    }


    /**
     * Метод генерации токенов
     * @param array $userData
     * @return Array
     * @throws \Exception
     */
    private function _generateTokens(Array $userData): Array
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));

        $userData['expireJwt'] = $now->modify('+1 minutes')->getTimestamp();
        $userData['expireRt'] = $now->modify('+2 minutes')->getTimestamp();

        $jwt = $this->_generateJwt($userData);
        $rt = $this->_generateRt($userData);

        $tokens = [
            'access_token' => $jwt,
            'refresh_token' => $rt,
            'expires_in' => $userData['expireJwt'],
            'expires_in_rt' => $userData['expireRt'],
        ];

        return $tokens;
    }


    /**
     * Генерация JWT
     * @param array $userData
     * @return String
     */
    private function _generateJwt(Array $userData): String
    {
        $token = array(
            "iss" => $this->_iss,
            "aud" => $this->_aud,
            "iat" => $this->_iat,
            "nbf" => $this->_nbf,
            "exp" => $userData['expireJwt'],
            "data" => array(
                "id" => $userData['id'],
                "name" => $userData['name'],
            )
        );

        return JWT::encode($token, $this->_key, 'HS256');
    }


    /**
     * Генерация RT
     * @param array $userData
     * @return String
     */
    private function _generateRt(Array $userData): String
    {
        $token = array(
            "iss" => $this->_iss,
            "aud" => $this->_aud,
            "iat" => $this->_iat,
            "nbf" => $this->_nbf,
            "exp" => $userData['expireRt'],
            "data" => array(
                "id" => $userData['id'],
                "name" => $userData['name'],
            )
        );

        return RT::encode($token, $this->_key, 'HS256');
    }

}