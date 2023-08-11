<?php


namespace engine\main\authentication\controllers;

use engine\base\controllers\BaseController;
use engine\base\controllers\Singleton;
use engine\main\authentication\models\MainModel;
use engine\main\authentication\libs\php_jwt\JWT;
use engine\main\authentication\libs\php_jwt\Key;

/**
 * Class AccessRightsController контроллер для проверки прав доступа
 * @package engine\main\authentication\controllers
 */
class AccessRightsController extends AuthenticationController
{

    private string $_key = '1111';

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
     * Мето для проверки авторизации
     * @return bool
     */
    public function isAuthorized($accessToken): int
    {
        try {
            $decodeData = (JWT::decode($accessToken, new Key($this->_key, 'HS256')))->data;
            $decodeData = json_decode(json_encode($decodeData), true);
            return $decodeData['id'];
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(array(
                'status' => 'error',
                'error' => 'invalid token',
            ));
            exit();
        }
    }


    /**
     * Метод для проверки доступа к данной странице
     * @param $url
     * @return bool|void
     */
    public function accessRightsCheck($url): bool
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