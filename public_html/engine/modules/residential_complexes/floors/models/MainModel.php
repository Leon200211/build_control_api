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

    private $__apartmentParamets = ['sockets', 'switches', 'toilet', 'sink', 'bath', 'floor_finishing',
        'draft_floor_department', 'ceiling_finishing', 'draft_ceiling_finish', 'wall_finishing', 'draft_wall_finish',
        'windowsill', 'kitchen', 'slopes', 'doors', 'wall_plaster', 'trash', 'radiator', 'floor_plaster',
        'ceiling_plaster', 'windows'];


    /**
     * Метод получения информации по квартире
     * @param $idApartment
     * @return array
     */
    public function getApartmentData($idApartment): array
    {
        $apartmentInfo = $this->read('apartments', [
            'fields' => ['id', 'apartment_number', 'sockets', 'switches', 'toilet', 'sink', 'bath', 'floor_finishing',
                         'draft_floor_department', 'ceiling_finishing', 'draft_ceiling_finish', 'wall_finishing', 'draft_wall_finish',
                         'windowsill', 'kitchen', 'slopes', 'doors', 'wall_plaster', 'trash', 'radiator', 'floor_plaster',
                         'ceiling_plaster', 'windows'],
            'where' => ['id' => $idApartment]
        ]);

        if (empty($apartmentInfo)) {
            return array();
        }

        return $apartmentInfo[0];
    }


    public function editApartmentData($idApartment, $data): bool
    {
        $fields = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $this->__apartmentParamets)) {
                $fields[$key] = trim(strip_tags($value));
            }
        }

        $this->update('apartments', [
           'fields' => $fields,
           'where' => ['id' => $idApartment]
        ]);

        return true;

    }

}