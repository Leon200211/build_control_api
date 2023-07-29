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
    public function outputData()
    {
        return json_encode($this->_response);
    }


    public function login()
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


    public function refreshTokens()
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


            unset($tokens['expires_in_rt']);
            $this->_response = $tokens;

        } catch (AuthException $e) {
            $this->_response = [
                'error' => $e
            ];
        }


    }


    public function logout()
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


    private function _generateTokens(Array $userData)
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





    /**
     * создание сессии
     * @return void
     */
    public function login2()
    {
        if(!$this->model) $this->model = MainModel::getInstance();

        // ищем пользователя при авторизации
        $user = $this->model->read('users', [
            'where' => [
                'login' => $_POST['username']
            ]
        ]);

        // если нашли пользователя, то создаем сессию
        if(!empty($user) and password_verify($_POST['password'], $user[0]['password'])){
            session_start();
            $_SESSION['id_user'] = $user[0]['id'];
            $_SESSION['name'] = $user[0]['name'];
            $_SESSION['role'] = $this->model->getUserRoles($user[0]['id']);
            $_SESSION['profile_img'] = $this->model->getUserImg($user[0]['id']);


            // Проверяем, что была нажата галочка 'Запомнить меня':
            if (!empty($_POST['remember']) and $_POST['remember'] == true) {
                //Сформируем случайную строку для куки (используем функцию):
                $key = uniqid(mt_rand(), true); //назовем ее $key

                //Пишем куки (имя куки, значение, время жизни - сейчас+месяц)
                @setcookie('id_user', $user[0]['id'], time()+60*60*24); //id_user
                @setcookie('login', $_POST['username'], time()+60*60*24); //логин
                @setcookie('key', $key, time()+60*60*24); //случайная строка
                /*
                    Пишем эту же куку в базу данных для данного юзера.
                    Формируем и отсылаем SQL запрос:
                    ОБНОВИТЬ  таблицу_users УСТАНОВИТЬ cookie = $key
                */
                $this->model->update('users', [
                    'fields' => ['cookie' => $key],
                    'where' => [
                        'id' => $user[0]['id'],
                        'login' => $_POST['username']
                    ]
                ]);

            }


            // записываем лог входа
            $this->model->add('login_history', [
                'fields' => [
                    'id_user' => $user[0]['id'],
                    'ip' => $this->getIp(),
                    'date' => 'NOW()'
                ]
            ]);


            $this->redirect('/');
        }else{
            $this->redirect('/login?error=Неверный логин или пароль');
        }

    }


    // метод получения ip пользователя
    protected function getIp() {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(end(explode(',', $_SERVER[$key])));
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }


    /**
     * уничтожение сессии
     * @return void
     */
    public function logout2()
    {

        if(isset($_SESSION['id_user']) or isset($_COOKIE['id_user'])){

            if(!$this->model) $this->model = MainModel::getInstance();

            $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : $_COOKIE['id_user'];

            // удаляем куку
            $this->model->update('users', [
                'fields' => ['cookie' => ''],
                'where' => [
                    'id' => $id_user
                ]
            ]);

            //Удаляем куки авторизации путем установления времени их жизни на текущий момент:
            setcookie('id_user', '', time()); //удаляем логин
            setcookie('login', '', time()); //удаляем логин
            setcookie('key', '', time()); //удаляем ключ

            session_destroy();

        }

    }


}