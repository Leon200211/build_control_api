<?php


namespace engine\main\profile\controllers;

use engine\base\controllers\BaseController;
use engine\base\controllers\Singleton;
use engine\base\exceptions\EmptyParameterException;
use engine\main\profile\models\MainModel;

/**
 * Class AccessRightsController контроллер для проверки прав доступа
 * @package engine\main\authentication\controllers
 */
class ProfileController extends AbstractProfileController
{

    private $_response;

    public function __construct()
    {
        $this->execBase();

    }


    public function outputData(): string
    {
        http_response_code(200);
        return json_encode($this->_response);
    }

    public function getMyProfile()
    {

        try {

            $data = [
                'access_token' => !empty($this->getFromHeader('Authorization')) ? $this->clearStr($this->getFromHeader('Authorization')) : throw new EmptyParameterException('Отсутствует параметр access_token'),
            ];
            $data['access_token'] = explode(' ', $data['access_token'])[1];

            $userId = $this->accessRightsChecker->isAuthorized($_REQUEST['access_token']);
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