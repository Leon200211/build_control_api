<?php


namespace engine\base\controllers;


/**
 * Class ExceptionalPagesController контроллер для исключительных страниц
 * @package engine\base\controllers
 */
class ExceptionalPagesController extends BaseController
{

    protected $renderFile;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->sendNoCacheHeaders();
    }


    /**
     * @return void
     */
    public function page404()
    {
        $this->renderFile = '404';
    }


    /**
     * @return false|string
     * @throws \engine\base\exceptions\RouteException
     */
    public function outputData()
    {
        return $this->render($_SERVER['DOCUMENT_ROOT'] . '/templates/default/' . $this->renderFile);
    }


    /**
     * запрет на кеширование
     * @return void
     */
    protected function sendNoCacheHeaders()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

}