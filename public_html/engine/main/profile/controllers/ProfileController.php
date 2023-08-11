<?php


namespace engine\main\profile\controllers;

use engine\base\controllers\BaseController;
use engine\base\controllers\Singleton;
use engine\base\exceptions\EmptyParameterException;
use engine\main\profile\models\MainModel;

/**
 * Class ProfileController контроллер для работы с профилями
 * @package engine\main\authentication\controllers
 */
class ProfileController extends AbstractProfileController
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
     * Метод получения данных моего профиля
     * @return void
     */
    public function getMyProfile(): void
    {
        try {
            $data = [
                'access_token' => !empty($this->getFromHeader('Authorization')) ? $this->clearStr($this->getFromHeader('Authorization')) : throw new EmptyParameterException('Отсутствует параметр access_token'),
            ];
            $data['access_token'] = explode(' ', $data['access_token'])[1];

            $userId = $this->accessRightsChecker->isAuthorized($data['access_token']);
            $userData = $this->model->getUserData($userId);
            $userData['profile_img'] = $this->getUserImg($userData['profile_img']);

            $this->_response = $userData;

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


    /**
     * Метод получения фотографии профиля
     * @param $img
     * @return string
     */
    private function getUserImg($img): string
    {
        if (@file_exists(USER_PROFILE_IMG . $img)) {
            $imagedata = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/' . USER_PROFILE_IMG . $img);
            $base64 = base64_encode($imagedata);

            return $base64;
        }

        return '';
    }

}