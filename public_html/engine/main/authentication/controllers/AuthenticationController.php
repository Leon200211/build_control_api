<?php


namespace engine\main\authentication\controllers;

use engine\main\authentication\controllers\AccessRightsController;
use engine\base\controllers\BaseController;
use engine\main\authentication\models\MainModel;
use engine\main\notificationSystem\controllers\Notification;


/**
 * Class AuthenticationController абстрактный контроллер модуля авторизации
 * @package engine\main\authentication\controllers
 */
abstract class AuthenticationController extends BaseController
{

    /**
     * @return void
     */
    protected function inputData()
    {

        if(!$this->model) $this->model = MainModel::getInstance();
        if(!$this->accessRightsChecker) $this->accessRightsChecker = AccessRightsController::getInstance();

        // запрет на кеширование
        $this->sendNoCacheHeaders();
    }


    /**
     * @return void
     */
    protected function execBase()
    {
        self::inputData();
    }


    /**
     *  запрет на кеширование
     * @return void
     */
    protected function sendNoCacheHeaders()
    {

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

    }

}