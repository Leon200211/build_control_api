<?php


namespace engine\main\authentication\controllers;


use engine\base\controllers\BaseController;
use engine\base\controllers\Singleton;
use engine\base\exceptions\AuthException;
use engine\main\authentication\libs\php_jwt\JWT;
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
        //return $this->render($_SERVER['DOCUMENT_ROOT'] . '/templates/default/login');
        echo 2;
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

            $userData = $this->model->checkAuthentication($loginData);

            $tokens = $this->_generateTokens($userData);

            echo json_encode($tokens);

        } catch (AuthException $e) {
            echo 123;
            //echo $e;
        }


    }

    private function _generateTokens(Array $userData)
    {
        $jwt = $this->_generateJwt($userData);
        return $jwt;
    }


    private function _generateJwt(Array $userData)
    {
        $token = array(
            "iss" => $this->_iss,
            "aud" => $this->_aud,
            "iat" => $this->_iat,
            "nbf" => $this->_nbf,
            "exp" => 1690477162,
            "data" => array(
                "id" => $userData['id'],
                "name" => $userData['name'],
            )
        );
        $jwt = JWT::encode($token, $this->_key, 'HS256');

        return $jwt;
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
    public function logout()
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