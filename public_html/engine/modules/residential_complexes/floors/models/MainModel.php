<?php


namespace engine\modules\residential_complexes\floors\models;

use engine\base\controllers\Singleton;
use engine\base\exceptions\AuthException;
use engine\base\models\BaseModel;


// основная модель при входе
class MainModel extends BaseModel
{

    // трейт для паттерна Singleton
    use Singleton;


    /**
     * Метод для получения всех домов по ЖК
     * @param $idProject
     * @return array
     */
    public function getFloor($idFloor): array
    {
        $floorData = $this->read('floors', [
           'fields' => ['id', 'floor_number', 'floor_plan_img'],
           'where' => ['id' => $idFloor]
        ]);

        if (empty($floorData)) {
            return array();
        }
        $floorData = $floorData[0];

        $floorData['apartments'] = $this->__getApartments($idFloor);

        return $floorData;
    }


    /**
     * Метод для получения проекта
     * @param $idProject
     * @return array
     */
    private function __getApartments($idFloor): array
    {
        $apartmentsInfo = $this->read('apartments', [
            'fields' => ['id', 'apartment_number'],
            'where' => ['id_floor' => $idFloor]
        ]);

        if (empty($apartmentsInfo)) {
            return array();
        }

        return $apartmentsInfo;
    }

}