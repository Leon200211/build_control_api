<?php


namespace engine\main\authentication\controllers;


use engine\base\controllers\BaseController;



/**
 * Class MainController основной контроллер для всех пользователей
 * @package engine\main\authentication\controllers
 */
class MainController extends AuthenticationController
{

    protected $title = 'PskVesna';


    /**
     * @return void
     */
    public function index()
    {
        $this->execBase();

        if(!$this->accessRightsChecker->isAutorized()){
            $this->redirect('/login');
        }
    }


    /**
     * @return false|string
     * @throws \engine\base\exceptions\RouteException
     */
    public function outputData()
    {
        return $this->render($_SERVER['DOCUMENT_ROOT'] . '/templates/default/index');
    }

}