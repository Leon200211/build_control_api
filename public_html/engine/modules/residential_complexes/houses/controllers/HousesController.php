<?php

namespace engine\modules\residential_complexes\houses\controllers;


use engine\base\exceptions\EmptyParameterException;

class HousesController extends AbstractHousesController
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
     * Метод для получения всех домов жк
     * @return void
     */
    public function getHouses(): void
    {
        try {
            $data = [
                'access_token' => !empty($this->getFromHeader('Authorization')) ? $this->clearStr($this->getFromHeader('Authorization')) : throw new EmptyParameterException('Отсутствует параметр access_token'),
            ];
            $data['access_token'] = explode(' ', $data['access_token'])[1];

            $userId = $this->accessRightsChecker->isAuthorized($data['access_token']);

            $data['id_projects'] = !empty($_POST['project_id']) ? $this->clearStr($_POST['project_id']) : throw new EmptyParameterException('Отсутствует параметр project_id');

            // Проверка на доступ к проекту

            $housesData = $this->model->getHouses($data['id_projects']);

            $this->_response = $housesData;
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