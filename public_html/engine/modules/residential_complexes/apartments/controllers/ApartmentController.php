<?php

namespace engine\modules\residential_complexes\apartments\controllers;

use engine\base\exceptions\EmptyParameterException;

class ApartmentController extends AbstractApartmentsController
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
     * Метод для получения информации по квартире
     * @return void
     */
    public function getApartmentInfo(): void
    {
        try {
            $data = [
                'access_token' => !empty($this->getFromHeader('Authorization')) ? $this->clearStr($this->getFromHeader('Authorization')) : throw new EmptyParameterException('Отсутствует параметр access_token'),
            ];
            $data['access_token'] = explode(' ', $data['access_token'])[1];

            $userId = $this->accessRightsChecker->isAuthorized($data['access_token']);

            $data['id_apartment'] = !empty($_POST['id_apartment']) ? $this->clearStr($_POST['id_apartment']) : throw new EmptyParameterException('Отсутствует параметр id_apartment');

            // Проверка какие проекты можно выдавать нашему пользователю
            $apartmentData = $this->model->getApartmentData($data['id_apartment']);

            $this->_response = $apartmentData;
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


    public function editApartmentInfo()
    {
        try {
            $data = [
                'access_token' => !empty($this->getFromHeader('Authorization')) ? $this->clearStr($this->getFromHeader('Authorization')) : throw new EmptyParameterException('Отсутствует параметр access_token'),
            ];
            $data['access_token'] = explode(' ', $data['access_token'])[1];

            $userId = $this->accessRightsChecker->isAuthorized($data['access_token']);

            $data = $_POST;
            $data['id_apartment'] = !empty($_POST['id_apartment']) ? $this->clearStr($_POST['id_apartment']) : throw new EmptyParameterException('Отсутствует параметр id_apartment');

            // Проверка какие проекты можно выдавать нашему пользователю

            $this->model->editApartmentData($data['id_apartment'], $data);

            $this->_response = [
                'status' => 'success'
            ];
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