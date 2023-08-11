<?php

namespace engine\modules\residential_complexes\floors\controllers;

use engine\base\exceptions\EmptyParameterException;

class FloorController extends AbstractFloorsController
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


    public function getFloor(): void
    {
        try {
            $data = [
                'access_token' => !empty($this->getFromHeader('Authorization')) ? $this->clearStr($this->getFromHeader('Authorization')) : throw new EmptyParameterException('Отсутствует параметр access_token'),
            ];
            $data['access_token'] = explode(' ', $data['access_token'])[1];

            $userId = $this->accessRightsChecker->isAuthorized($data['access_token']);

            $data['id_floor'] = !empty($_POST['id_floor']) ? $this->clearStr($_POST['id_floor']) : throw new EmptyParameterException('Отсутствует параметр id_floor');

            // Проверка какие проекты можно выдавать нашему пользователю
            $floorData = $this->model->getFloor($data['id_floor']);

            $this->_response = $floorData;
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