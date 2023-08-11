<?php


namespace engine\modules\residential_complexes\houses\models;

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
    public function getHouses($idProject): array
    {
        $housesData['project'] = $this->__getProject($idProject);

        $housesData['houses'] = $this->read('houses', [
           'fields' => ['id', 'house_number', 'title', 'address', 'section_img'],
           'where' => ['id_project' => $idProject]
        ]);

        return $housesData;
    }


    /**
     * Метод для получения проекта
     * @param $idProject
     * @return array
     */
    private function __getProject($idProject): array
    {
        $projectInfo = $this->read('projects', [
            'fields' => ['id', 'project_number', 'title', 'address', 'img'],
            'where' => ['id' => $idProject]
        ]);

        if (empty($projectInfo)) {
            return array();
        }

        return $projectInfo[0];
    }


    /**
     * Метод для получения дома
     * @param $idHouse
     * @return array
     */
    public function getHouse($idHouse): array
    {
        $housesData = $this->read('houses', [
            'fields' => ['id', 'id_project', 'house_number', 'title', 'address', 'section_img'],
            'where' => ['id' => $idHouse],
        ]);

        if (empty($housesData)) {
            return array();
        } else {
            $housesData = $housesData[0];
        }

        $housesData['sections'] = $this->__getSectionsByHouse($idHouse);

        return $housesData;
    }


    /**
     * Метод получения секций дома
     * @param $idHouse
     * @return array
     */
    private function __getSectionsByHouse($idHouse): array
    {
        $sections = $this->read('sections', [
            'fields' => ['id', 'section_number'],
            'where' => ['id_house' => $idHouse],
        ]);

        if (empty($sections)) {
            return array();
        }

        return $sections;
    }


    /**
     * Метод для получения проекта
     * @param $idHouse
     * @return int
     * @throws \Exception
     */
    public function searchProjectByHouse($idHouse): int
    {
        $projectId = $this->read('houses', [
            'fields' => ['id_project'],
            'where' => ['id' => $idHouse]
        ]);

        if (empty($projectId)) {
            throw new \Exception("Не валидный параметр");
        }

        return $projectId[0]['id_project'];
    }

}