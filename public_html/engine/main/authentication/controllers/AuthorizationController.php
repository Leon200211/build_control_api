<?php


namespace engine\main\authentication\controllers;


use engine\base\controllers\BaseController;
use engine\base\controllers\Singleton;
use engine\base\exceptions\AuthException;
use engine\base\exceptions\EmptyParameterException;
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
        http_response_code(200);
        return json_encode($this->_response);
    }


    /**
     * Метод авторизации
     * @return void
     */
    public function login(): void
    {
        try {
            $loginData = [
                'username' => !empty($_POST['username']) ? $this->clearStr($_POST['username']) : throw new EmptyParameterException('Отсутствует параметр username'),
                'password' => !empty($_POST['password']) ? $this->clearStr($_POST['password']) : throw new EmptyParameterException('Отсутствует параметр password'),
                'fingerprint' => !empty($_POST['fingerprint']) ? $this->clearStr($_POST['fingerprint']) : throw new EmptyParameterException('Отсутствует параметр fingerprint'),
            ];

            // проверят подлинность логина/пароля
            $userData = $this->model->checkAuthentication($loginData);

            // Генерируем токены
            $tokens = $this->_generateTokens($userData);

            // Проверяем refreshSessions и делаем запись
            $this->model->refreshSession($userData, $tokens);

            $this->_response = $tokens;
        } catch (EmptyParameterException $parameterException) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'error' => $parameterException->getMessage()
            ]);
            exit();
        } catch (AuthException $authException) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'error' => $authException->getMessage(),
            ]);
            exit();
        }
    }


    /**
     * Метод обновления токенов
     * @return void
     */
    public function refreshTokens(): void
    {
        try {
            $data = [
                'user_id' => !empty($_POST['user_id']) ? $this->clearStr($_POST['user_id']) : throw new EmptyParameterException('Отсутствует параметр user_id'),
                'refresh_token' => !empty($_POST['refresh_token']) ? $this->clearStr($_POST['refresh_token']) : throw new EmptyParameterException('Отсутствует параметр refresh_token'),
                'fingerprint' => !empty($_POST['fingerprint']) ? $this->clearStr($_POST['fingerprint']) : throw new EmptyParameterException('Отсутствует параметр fingerprint'),
            ];

            $this->model->checkRefreshSession($data);

            // Получаем данные пользователя из БД
            $userData = $this->model->getUserData($data['user_id']);

            // Генерируем токены
            $tokens = $this->_generateTokens($userData);

            // Проверяем refreshSessions и делаем запись
            $this->model->refreshSession($userData, $tokens);

            $this->_response = $tokens;
        } catch (EmptyParameterException $parameterException) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'error' => $parameterException->getMessage()
            ]);
            exit();
        } catch (AuthException $authException) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'error' => $authException->getMessage(),
            ]);
            exit();
        }
    }


    /**
     * Метод деавторизации
     * @return void
     */
    public function logout(): void
    {
        try {
            $data = [
                'user_id' => !empty($_POST['user_id']) ? $this->clearStr($_POST['user_id']) : throw new EmptyParameterException('Отсутствует параметр user_id'),
                'refresh_token' => !empty($_POST['refresh_token']) ? $this->clearStr($_POST['refresh_token']) : throw new EmptyParameterException('Отсутствует параметр refresh_token'),
                'fingerprint' => !empty($_POST['fingerprint']) ? $this->clearStr($_POST['fingerprint']) : throw new EmptyParameterException('Отсутствует параметр fingerprint'),
            ];

            $this->model->deleteRefreshSession($data);

            $this->_response = [
                'status' => 'success',
            ];
        } catch (EmptyParameterException $parameterException) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'error' => $parameterException->getMessage()
            ]);
            exit();
        } catch (AuthException $authException) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'error' => $authException->getMessage(),
            ]);
            exit();
        }
    }


    /**
     * Метод для проверки jwt
     * @param string $jwt
     * @return bool
     */
    public function validateToken(string $jwt=''): bool
    {
        $jwt = $this->clearStr($_POST['jwt']);
        try {
            // Декодирование jwt
            $decoded = JWT::decode($jwt, new Key($this->_key, 'HS256'));
        } catch (\Exception $expiredException) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'error' => 'invalid token',
            ]);
            exit();
        }
        return true;
    }


    /**
     * Метод генерации токенов
     * @param array $userData
     * @return array
     * @throws \Exception
     */
    private function _generateTokens(Array $userData): array
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));

        $userData['expireJwt'] = $now->modify('+20 minutes')->getTimestamp();
        $userData['expireRt'] = $now->modify('+30 days')->getTimestamp();

        $jwt = $this->_generateJwt($userData);
        $rt = $this->_generateRt($userData);

        $tokens = [
            'access_token' => $jwt,
            'refresh_token' => $rt,
            'at_expires_in' => $userData['expireJwt'],
            'rt_expires_in' => $userData['expireRt'],
        ];

        return $tokens;
    }


    /**
     * Генерация JWT
     * @param array $userData
     * @return String
     */
    private function _generateJwt(Array $userData): string
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
    private function _generateRt(Array $userData): string
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