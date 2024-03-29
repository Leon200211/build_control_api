<?php


namespace engine\base\models;


/**
 * Class BaseModelMethods класс хранит вспомогательные методы для crud
 * @package engine\base\models
 */
abstract class BaseModelMethods
{
    // массив встроенных функций в mySql
    protected $mySql_function = ['NOW()', 'CURDATE()', 'UNIX_TIMESTAMP()'];


    // ===================================================
    // Для SELECT
    // ===================================================


    /**
     * Группировка всех полей для вывода и работы
     * @param $set
     * @param $table
     * @return string
     */
    protected function createFields($set, $table = false)
    {
        // проверка на существование полей
        // проверка на существование полей
        if (empty($set['fields'])) {
            return $table.'.*';
        }

        $set['fields'] = (!empty($set['fields']) and is_array($set['fields']))
            ? $set['fields'] : $table.'.*';
        $table = ($table && !isset($set['no_concat'])) ? $table . '.' : '';
        $fields = '';

        foreach ($set['fields'] as $field){
            if(strpos($field, ' as ')){
                $fields .= $table . '`' . substr($field, 0, strpos($field, ' as ')) . '`' . substr($field, strpos($field, ' as ')) . ',';
            }else{
                $fields .= $table . '`' . $field . '`' . ',';
            }
        }
        // обрезаем запятую
        $fields = rtrim($fields, ',');

        return $fields;
    }


    /**
     * создание запроса для конструкции Where
     * @param $set
     * @param $table
     * @param $instruction
     * @return string
     */
    protected function createWhere($set, $table = false, $instruction = 'WHERE')
    {
        $table = ($table && !isset($set['no_concat'])) ? $table . '.' : '';
        $where = '';

        if (isset($set['where']) and is_string($set['where'])) {
            return $instruction . ' ' . trim($set['where']);
        }

        if (!empty($set['where']) and is_array($set['where']))
        {
            // пришли ли операнды
            $set['operand'] = (!empty($set['operand']) and is_array($set['operand'])) ? $set['operand'] : ['='];
            // пришли ли условия
            $set['condition'] = (!empty($set['condition']) and is_array($set['condition'])) ? $set['condition'] : ['AND'];
            $where = $instruction;
            $o_count = 0;
            $c_count = 0;

            foreach ($set['where'] as $key => $item) {
                $where .= " ";
                // определяем операнд
                if (isset($set['operand'][$o_count])) {
                    $operand = $set['operand'][$o_count];
                    $o_count++;
                } else {
                    $operand = $set['operand'][$o_count-1];
                }

                // определяем условие
                if (isset($set['condition'][$c_count])) {
                    $condition = $set['condition'][$c_count];
                    $c_count++;
                } else {
                    $condition = $set['condition'][$c_count-1];
                }

                if ($operand === 'IN' or $operand === 'NOT IN') {
                    if (is_string($item) and strpos($item, 'SELECT') === 0) {
                        $in_str = $item;
                    } else {
                        if (is_array($item)) {
                            $temp_item = $item;
                        } else {
                            $temp_item = explode(',', $item);
                        }
                        $in_str = '';

                        foreach ($temp_item as $v) {
                            $in_str .= "'" . addslashes(trim($v)) . "',";
                        }
                    }

                    $where .= $table . $key . ' ' . $operand . " (" . rtrim($in_str, ',') . ") " . $condition;

                } elseif (strpos($operand, 'LIKE') !== false) {
                    $like_template = explode('%', $operand);

                    foreach ($like_template as $lt_key => $lt) {
                        if (!$lt) {
                            if (!$lt_key) {
                                $item = '%' . $item;
                            } else {
                                $item .= '%';
                            }
                        }
                    }
                    $where .= $table . $key . " LIKE '" . addslashes($item) . "' $condition";
                } else {
                    // проверка на подзапросы
                    if (strpos($item, 'SELECT') === 0) {
                        $where .= $table . $key . $operand . '(' . $item . ') ' . $condition;
                    } else {
                        if (in_array($item, $this->mySql_function)) {
                            $where .= $table . $key . $operand . ' ' . addslashes($item) . " " . $condition;
                        } else {
                            $where .= $table . $key . $operand . "'" . addslashes($item) . "' " . $condition;
                        }
                    }
                }

            }
            // убираем последнее условие
            $where = substr($where, 0, strrpos($where, $condition));
        }

        return $where;
    }


    /**
     * создание join запроса
     * @param $set
     * @param $table
     * @param $new_where
     * @param $join_key
     * @return array
     */
    protected function createJoin($set, $table, $new_where = false, $join_key = 'join')
    {
        $fields = '';
        $join = '';
        $where = '';
        $tables = '';

        if (isset($set[$join_key])) {
            $join_table = $table;
            foreach ($set[$join_key] as $key => $item) {
                if (is_int($key)) {
                    if (!$item['table']) {
                        continue;
                    } else{
                        $key = $item['table'];
                    }
                }

                if ($join) {
                    $join .= ' ';
                }

                if (isset($item['on']) and $item['on']) {
                    $join_fields = [];

                    if (isset($item['on']['fields']) and is_array($item['on']['fields']) and count($item['on']['fields']) === 2) {
                        $join_fields = $item['on']['fields'];
                    } else if(count($item['on']) === 2) {
                        $join_fields = $item['on'];
                    } else {
                        // для скипа этой итерации в которые мы вложены
                        //continue 2;
                        continue;
                    }

                    if (!$item['type']) {
                        $join .= 'LEFT JOIN ';
                    } else {
                        $join .= trim(strtoupper($item['type'])) . ' JOIN ';
                    }

                    $join .= $key . ' ON ';

                    // проверка с какой таблицей стыковаться
                    if(@$item['on']['table']){
                        $join .= $item['on']['table'];
                    }else{
                        $join .= $join_table;
                    }

                    // указания полей для стыковки
                    $join .= '.' . $join_fields[0] . '=' . $key . '.' . $join_fields[1];

                    $join_table = $key;
                    $tables .= ", " . trim($join_table);

                    if ($new_where) {
                        if ($item['where']) {
                            $new_where = false;
                        }
                        $group_condition = 'WHERE';
                    } else {
                        $group_condition = isset($item['group_condition']) ? strtoupper($item['group_condition']) : 'AND';
                    }

                    $fields .= $this->createFields($item, $key);
                    $fields = ',' . $fields;

                    $where .= $this->createWhere($item, $key, $group_condition);
                }
            }
        }

        return compact('fields', 'join', 'where', 'tables');
    }


    /**
     * создание запроса сортировки
     * @param $set
     * @param $table
     * @return string
     */
    protected function createOrder($set, $table = false)
    {
        $table = ($table && !isset($set['no_concat'])) ? $table . '.' : '';
        $order_by = '';
        if (!empty($set['order']) and is_array($set['order'])) {
            $set['order_direction'] = (!empty($set['order_direction']) and is_array($set['order_direction']))
                ? $set['order_direction'] : ['ASC'];

            $order_by = 'ORDER BY ';
            $direct_count = 0;
            foreach ($set['order'] as $order) {
                // направление сортировки
                if (@$set['order_direction'][$direct_count]) {
                    $order_direction = strtoupper($set['order_direction'][$direct_count]);
                    $direct_count++;
                } else {
                    $order_direction = strtoupper($set['order_direction'][$direct_count-1]);
                }
                if (is_int($order)) {
                    $order_by .= $order . ' ' . $order_direction . ',';
                } else {
                    $order_by .= $table . $order . ' ' . $order_direction . ',';
                }
            }

            // обрезаем запятую
            $order_by = rtrim($order_by, ',');
        }

        return $order_by;
    }



    // ===================================================
    // Для INSERT
    // ===================================================

    /**
     * Метод создания insert запроса
     * @param $fields
     * @param $files
     * @param $except
     * @return array
     */
    protected function createInsert($fields, $files, $except)
    {
        $insert_arr = [];

        $insert_arr['fields'] = "(";
        $insert_arr['values'] = "";

        // проверка на многомерный массив
        $array_type = array_keys($fields)[0];
        if(is_int($array_type)) {
            $check_fields = false;
            $count_fields = 0;

            foreach ($fields as $i => $item) {
                $insert_arr['values'] .= "(";
                // во всех множествах должно быть одинаковое кол-во элементов
                if (!$count_fields) {
                    $count_fields = count($item);
                }

                // счетчик количества полей в создании записи
                $j = 0;

                foreach ($item as $row => $value) {
                    // если в подмножестве есть то, что нужно пропустить
                    if($except and in_array($row, $except)) continue;

                    if(!$check_fields) $insert_arr['fields'] .= $row . ',';

                    // если передаем функции sql
                    if (in_array($value, $this->mySql_function)) {
                        $insert_arr['values'] .= $value . ',';
                    } elseif ($value == "NULL" or $value === NULL) {
                        $insert_arr['values'] .= "NULL" . ',';
                    } else {
                        $insert_arr['values'] .= "'" . addslashes($value) . "',";
                    }
                    $j++;
                    // проверка на кол-во полей в записе
                    if ($j === $count_fields) break;
                }

                // если след. запись меньше чем $count_fields, то добавим пустые ячейки
                if ($j < $count_fields) {
                    for(; $j < $count_fields; $j++){
                        $insert_arr['values'] .= "NULL";
                    }
                }

                // удаляем запятую в конце одной записи и ставим запятую перед след записью
                $insert_arr['values'] = rtrim($insert_arr['values'], ',') . "),";
                // сохранили название полей для insert
                if (!$check_fields) $check_fields = true;
            }
        } else {
            $insert_arr['fields'] = "(";
            $insert_arr['values'] = "(";
            if ($fields) {
                foreach ($fields as $row => $value) {
                    // если вставить кроме
                    if ($except and in_array($row, $except)) continue;

                    @$insert_arr['fields'] .= '`' . $row . '`' . ',';

                    if (in_array($value, $this->mySql_function)) {
                        $insert_arr['values'] .= $value . ',';
                    } elseif ($value == "NULL" or $value === NULL) {
                        $insert_arr['values'] .= "NULL" . ',';
                    } else {
                        $insert_arr['values'] .= "'" . addslashes($value) . "',";
                    }
                }
            }

            if ($files) {
                foreach ($files as $row => $file) {
                    @$insert_arr['fields'] .= $row . ',';
                    if (is_array($file)) {
                        @$insert_arr['values'] .= "'" . addslashes(json_encode($file)) . "',";
                    } else {
                        @$insert_arr['values'] .= "'" . addslashes($file) . "',";
                    }
                }
            }

            // удаляем запятую в конце одной записи и закрываем скобку
            $insert_arr['values'] = rtrim($insert_arr['values'], ',') . ")";
        }

        // удаляем запятую в конце
        $insert_arr['fields'] = rtrim($insert_arr['fields'], ',') . ')';
        $insert_arr['values'] = rtrim($insert_arr['values'], ',');

        return $insert_arr;
    }



    // ===================================================
    // Для UPDATE
    // ===================================================
    /**
     * Метод создания Update запроса
     * @param $fields
     * @param $files
     * @param $except
     * @return string
     */
    protected function createUpdate($fields, $files, $except){
        $update = '';
        if ($fields) {
            foreach ($fields as $row => $value) {
                // если обновить кроме
                if($except and in_array($row, $except)) continue;

                $update .= $row . "=";
                if (in_array($value, $this->mySql_function)) {
                    $update .= $value . ',';
                } else if($value === NULL) {
                    $update .= "NULL" . ',';
                } else {
                    $update .= "'" . addslashes($value) . "',";
                }
            }
        }

        if ($files) {
            foreach ($files as $row => $file) {
                $update .= $row . '=';
                if (is_array($file)) {
                    $update .= "'" . addslashes(json_encode($file)) . "',";
                } else {
                    $update .= "'" . addslashes($file) . "',";
                }
            }
        }

        return rtrim($update, ',');
    }

}