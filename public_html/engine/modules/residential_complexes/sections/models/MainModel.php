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
     * Метод для получения секции
     * @param $id_house
     * @return array
     */
    public function getSection($idSection): array
    {
        $sections = $this->read('sections', [
           'fields' => ['id', 'section_number'],
           'where' => ['id' => $idSection]
        ]);

        if (empty($sections)) {
            return array();
        }
        $sections = $sections[0];

        $sections['floor'] = $this->__getFloors($idSection);

        return $sections;
    }


    /**
     * Метод получения всех этажей
     * @param $idSection
     * @return array
     */
    private function __getFloors($idSection): array
    {
        $floor = $this->read('floors', [
           'fields' => ['id', 'floor_number'],
           'where' => ['id_section' => $idSection]
        ]);

        if (empty($floor)) {
            return array();
        }

        return $floor;
    }

}