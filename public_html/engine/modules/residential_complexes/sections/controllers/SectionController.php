<?php

namespace engine\modules\residential_complexes\sections\controllers;

use engine\base\exceptions\EmptyParameterException;

class SectionController extends AbstractSectionsController
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
     * Метод получения информации по сеции
     * @return void
     */
    public function getSection(): void
    {
        try {
            $data = [
                'access_token' => !empty($this->getFromHeader('Authorization')) ? $this->clearStr($this->getFromHeader('Authorization')) : throw new EmptyParameterException('Отсутствует параметр access_token'),
            ];
            $data['access_token'] = explode(' ', $data['access_token'])[1];

            $userId = $this->accessRightsChecker->isAuthorized($data['access_token']);

            $data['id_section'] = !empty($_POST['id_section']) ? $this->clearStr($_POST['id_section']) : throw new EmptyParameterException('Отсутствует параметр id_house');


            // Проверка какие проекты можно выдавать нашему пользователю
            $sectionData = $this->model->getSection($data['id_section']);

            $this->_response = $sectionData;
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