<?php


namespace engine\main\authentication\controllers;

use engine\base\controllers\BaseController;
use engine\base\controllers\Singleton;
use engine\main\authentication\models\MainModel;

/**
 * Class AccessRightsController контроллер для проверки прав доступа
 * @package engine\main\authentication\controllers
 */
class AccessRightsController extends AuthenticationController
{

    // трейт для паттерна Singleton
    use Singleton;

    // массив прав доступа
    private $pagesAccess = [

        '/404' =>[
            'admin', 'ceo', 'laser_workshop_boss', 'laser_machine_operator', 'manager'
        ],

        '/' => [
            'admin', 'ceo', 'laser_workshop_boss', 'laser_machine_operator', 'manager'
        ],
        '/login' => [
            'admin', 'ceo', 'laser_workshop_boss', 'laser_machine_operator', 'manager'
        ],


    ];


    /**
     * метод для проверки авторизации
     *     Если пользователь не авторизован (проверяем по сессии) -
            тогда проверим его куки, если в куках есть логин и ключ,
            то пробьем их по базе данных.
            Если пара логин-ключ подходит - пишем авторизуем пользователя.

            Если пользователь авторизован - ничего не делаем.
            Поэтому этот код должен вызываться всегда при заходе пользователя на сайт -
            нагрузку на сервер он не создает.
     * @return bool
     */
    public function isAutorized()
    {

        $this->execBase();

        if(isset($_SESSION['id_user'])){
            return true;
        }

        //Проверяем, не пустые ли нужные нам куки...
        if ( !empty($_COOKIE['login']) and !empty($_COOKIE['key']) ) {
            //Пишем логин и ключ из КУК в переменные (для удобства работы):
            $id_user = $_COOKIE['id_user'];
            $login = $_COOKIE['login'];
            $key = $_COOKIE['key']; //ключ из кук (аналог пароля, в базе поле cookie)

            /*
                Формируем и отсылаем SQL запрос:
                ВЫБРАТЬ ИЗ таблицы_users ГДЕ поле_логин = $login.
            */

            $result = $this->model->read('users', [
                'where' => [
                    'id' => $id_user,
                    'login' => $login,
                    'cookie' => $key
                ]
            ]);
            if (!empty($result[0])) {
                //Стартуем сессию:
                session_start();
                $_SESSION['id_user'] = $result[0]['id'];
                $_SESSION['name'] = $result[0]['name'];
                $_SESSION['role'] = $this->model->getUserRoles($result[0]['id']);
                $_SESSION['profile_img'] = $this->model->getUserImg($result[0]['id']);

                return true;
            }
        }

        return false;
    }


    /**
     * метод для проверки доступа к данной странице
     * @param $url
     * @return bool|void
     */
    public function accessRightsCheck($url)
    {

        // если есть аргументы GET
        if($_SERVER['QUERY_STRING']){
            $url = substr($url, 0, strpos($url, $_SERVER['QUERY_STRING']) - 1);
        }

        // проверка на допуск к этой странице
        foreach ($_SESSION['role'] as $item){
            if(in_array($item['role_title'], $this->pagesAccess[$url])){
                return true;
            }
        }

        $this->redirect('/404');

    }


}