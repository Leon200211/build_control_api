<?php


namespace engine\base\controllers;


/**
 * трейт для реализации базовых методов,
 * используемых во многих классах
 */
trait BaseMethods
{

    /**
     * Метод проверки, есть ли одна из наших ролей в списке
     * @param $roles
     * @return bool
     */
    protected function roleArrayIntersect($roles)
    {
        if (gettype($roles) === 'string') {
            $str = $roles;
            $roles = [$roles];
        }

        $user_roles = [];
        // Получаем название всех наших ролей
        foreach ($_SESSION['role'] as $item) {
            $user_roles[] = $item['role_title'];
        }

        // проверяем есть ли вхождение нашей роли в систему
        if (!empty(array_intersect($roles, $user_roles))) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Метод для очистки сток
     * @param $str
     * @return array|string
     */
    protected function clearStr($str)
    {
        // если пришел массив значений
        if (is_array($str)) {
            foreach ($str as $key => $item) {
                $str[$key] = trim(strip_tags($item));  // strip_tags — Удаляет теги HTML и PHP из строки
            }
            return $str;
        } else {
            return trim(strip_tags($str));
        }

    }


    /**
     * Метод для отчистки числовых данных
     * @param $num
     * @return float|int
     */
    protected function clearNum($num)
    {
        return $num * 1;
    }


    /**
     * Проверка на post запрос
     * @return bool
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }


    /**
     * Проверка на ajax
     * @return bool
     */
    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }


    /**
     * метод для редирект страниц
     * @param $http
     * @param $code
     * @return void
     */
    protected function redirect($http = false, $code = false)
    {
        if ($code) {
            $codes = ['301' => 'HTTP/1.1 301 Move Permanently'];

            // существует ли такой элемент
            if ($codes[$code]) {
                header($codes[$code]);
            }
        }

        if ($http) {
            $redirect = $http;
        } else {
            $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : PATH;
        }

        header("Location: $redirect");

        exit;
    }


    /**
     * метод для записи логи
     * @param $message
     * @param $file
     * @param $event
     * @return void
     */
    protected function writeLog($message, $file = 'log.txt', $event = 'Fault')
    {
        $dataTime = new \DateTime();
        $str = $event . ': ' . $dataTime->format('d-m-Y G:i:s') . ' - ' . $message . "\r\n";

        file_put_contents('log/' . $file, $str, FILE_APPEND);
    }

}