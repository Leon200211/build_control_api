<?php


namespace engine\modules\residential_complexes\projects\models;

use engine\base\controllers\Singleton;
use engine\base\exceptions\AuthException;
use engine\base\models\BaseModel;


// основная модель при входе
class MainModel extends BaseModel
{

    // трейт для паттерна Singleton
    use Singleton;


    /**
     * Метод для получения данных по проектам
     * @param $userId
     * @return array
     */
    public function getProjects($userId): array
    {
        // Вызываем метод, который вернет массив id проектов доступных пользователю

        $projects = $this->read('projects', [
            'fields' => ['id', 'project_number', 'title', 'address', 'img'],
            'where' => ['id' => [1, 2, 3]],
            'operand' => ['IN']
        ]);

        if (empty($projects)) {
            return array();
        }

        return $projects;
    }



}