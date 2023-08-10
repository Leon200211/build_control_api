<?php

namespace engine\modules\residential_complexes\houses\controllers;


use engine\base\exceptions\EmptyParameterException;

class HouseController extends AbstractHousesController
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
     * Метод для получения дома
     * @return void
     */
    public function getHouse()
    {
        try {
            $data = [
                'access_token' => !empty($this->getFromHeader('Authorization')) ? $this->clearStr($this->getFromHeader('Authorization')) : throw new EmptyParameterException('Отсутствует параметр access_token'),
            ];
            $data['access_token'] = explode(' ', $data['access_token'])[1];

            $userId = $this->accessRightsChecker->isAuthorized($data['access_token']);

            $data['id_house'] = !empty($_POST['id_house']) ? $this->clearStr($_POST['id_house']) : throw new EmptyParameterException('Отсутствует параметр id_house');

            // получение id проекта
            $projectId = $this->model->searchProjectByHouse($data['id_house']);
            // Проверка на доступ к проекту

            $houseData = $this->model->getHouse($data['id_house']);

            $this->_response = $houseData;
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