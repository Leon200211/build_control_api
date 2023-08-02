<?php


namespace engine\main\profile\controllers;

use engine\main\authentication\controllers\AccessRightsController;
use engine\base\controllers\BaseController;
use engine\main\profile\models\MainModel;


/**
 * Class AuthenticationController абстрактный контроллер модуля авторизации
 * @package engine\main\authentication\controllers
 */
abstract class AbstractProfileController extends BaseController
{

    /**
     * @return void
     */
    protected function inputData(): void
    {

        if(!$this->model) $this->model = MainModel::getInstance();
        if(!$this->accessRightsChecker) $this->accessRightsChecker = AccessRightsController::getInstance();

        // запрет на кеширование
        $this->sendNoCacheHeaders();
    }


    /**
     * @return void
     */
    protected function execBase(): void
    {
        self::inputData();
    }


    /**
     *  Запрет на кеширование
     * @return void
     */
    protected function sendNoCacheHeaders(): void
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

}