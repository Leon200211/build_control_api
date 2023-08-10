<?php

namespace engine\modules\residential_complexes\projects\controllers;

use engine\base\exceptions\EmptyParameterException;

class ProjectsController extends AbstractProjectsController
{
    private array $_response;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->execBase();
    }


    /**
     * @return string
     */
    public function outputData(): string
    {
        http_response_code(200);
        return json_encode($this->_response);
    }


    /**
     * Метод для получения всех ЖК
     * @return void
     */
    public function getProjects()
    {
        // Здесь параметры для фильтра

        try {
            $data = [
                'access_token' => !empty($this->getFromHeader('Authorization')) ? $this->clearStr($this->getFromHeader('Authorization')) : throw new EmptyParameterException('Отсутствует параметр access_token'),
            ];
            $data['access_token'] = explode(' ', $data['access_token'])[1];

            $userId = $this->accessRightsChecker->isAuthorized($data['access_token']);

            // Проверка какие проекты можно выдавать нашему пользователю
            $projectsData = $this->model->getProjects($userId);

            $this->_response = $projectsData;
        } catch (EmptyParameterException $parameterException) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'error' => $parameterException->getMessage()
            ]);
            exit();
        } catch (\Exception $exception) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'error' => $exception->getMessage()
            ]);
            exit();
        }
    }

}