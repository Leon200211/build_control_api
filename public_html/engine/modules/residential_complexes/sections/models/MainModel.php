<?php


namespace engine\modules\residential_complexes\sections\models;

use engine\base\controllers\Singleton;
use engine\base\exceptions\AuthException;
use engine\base\models\BaseModel;


// основная модель при входе
class MainModel extends BaseModel
{

    // трейт для паттерна Singleton
    use Singleton;


    /**
     * Метод для получения секций
     * @param $id_house
     * @return array
     */
    public function getSections($id_house): array
    {
        $sections = $this->read('sections', [
           'fields' => ['id', 'section_number'],
           'where' => ['id_house' => $id_house]
        ]);

        if (empty($sections)) {
            return array();
        }

        return $sections;
    }

}