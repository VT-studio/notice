/*
Функции для работы с бд на вердпресс
Можно переделать под что угодно
*/

<?php
/**
 * @param string $table_name
 * @param array $params
 * @return array
 * $params = ['id' => 255, 'uom' => 'BX'] or $params = ['>', 'total', 0 ]
 */
function __getResultArray($table_name, $params = ['id' => 1])
{
    global $wpdb;
    $sql = "SELECT * FROM `{$table_name}`";
    $i = 0;

    if (isset($params[0]) && count($params) == 3){
        $sql .= " WHERE " . "`{$params[1]}` {$params[0]} '{$params[2]}'";
    }else{
        foreach ($params as $key => $value) {
            $sql .= $i > 0 ? " AND `{$key}` = '{$value}'" : " WHERE `{$key}` = '{$value}'";
            $i++;
        }
    }
    $result = $wpdb->get_row($sql, ARRAY_A);

    return $result;
}

/**
 * @param string $table_name
 * @param string $count_value
 * @param array $filter
 *
 * @var array $filter['__where']
 * @var array $filter['__not_where']
 * @var array $filter['__like']
 * @var array $filter['__more']
 * @var array $filter['__less']
 *
 * @return array
 *
 *
 * $count_value - what is count, default it is 'id'
 * $filter = [
 *  '__where' => ['asin' => 'adcde445566', 'upc' => '1236544587'], //равно
 *  '__not_where' => ['asin' => 'adcde445566'], // не равно
 *  '__like' => ['name' => 'abc', 'first_name' => 'cba'],
 *  '__more' => ['id' => 20, 'id_customer' => 50],  //больше
 *  '__less' => ['id' => 20, 'id_customer' => 50]  //меньше
 *
 * ]
 */
function __getTotalCount($table_name, $count_value = 'id', $filter = [])
{
    global $wpdb;

    $sql = "SELECT COUNT(`{$count_value}`) FROM {$table_name}";

    if (isset($filter['__more'])){
        $i = 0;
        foreach ($filter['__more'] as $key => $value){

            if ((int)$value) {
                $sql .= $i > 0 ? " AND `{$key}` > '{$value}'" : " WHERE `{$key}` > '{$value}'";
            }else{
                return $result['error'] = 'error more value';
            }
            $i++;
        }
    }

    if (isset($filter['__less'])){
        if (!isset($filter['__more'])) {
            $i = 0;
            foreach ($filter['__less'] as $key => $value) {
                if ((int)$value) {
                    $sql .= $i > 0 ? " AND `{$key}` < '{$value}'" : " WHERE `{$key}` < '{$value}'";
                } else {
                    return $result['error'] = 'error more value';
                }
                $i++;
            }
        }else{
            foreach ($filter['__less'] as $key => $value) {
                if ((int)$value) {
                    $sql .= " AND `{$key}` < '{$value}'";
                } else {
                    return $result['error'] = 'error less value';
                }
            }
        }
    }

    if (isset($filter['__where'])) {
        if (!isset($filter['__more']) && !isset($filter['__less'])){
            $i = 0;
            foreach ($filter['__where'] as $key => $value) {
                if ($value != '') {
                    $sql .= $i > 0 ? " AND `{$key}` = '{$value}'" : " WHERE `{$key}` = '{$value}'";
                    $i++;
                }
            }
        }else{
            foreach ($filter['__where'] as $key => $value) {
                if ($value != '')
                    $sql .= " AND `{$key}` = '{$value}'";
            }
        }

    }


    if (isset($filter['__not_where'])) {
        if (!isset($filter['__more']) && !isset($filter['__less']) && !isset($filter['__where'])){
            $i = 0;
            foreach ($filter['__not_where'] as $key => $value) {
                $sql .= $i > 0 ? " AND `{$key}` != '{$value}'" : " WHERE `{$key}` != '{$value}'";
                $i++;
            }
        }else{
            foreach ($filter['__not_where'] as $key => $value) {
                $sql .= " AND `{$key}` != '{$value}'";
            }
        }

    }



    if (isset($filter['__like'])){
        if (!isset($filter['__more']) && !isset($filter['__less']) && !isset($filter['__where']) && !isset($filter['__not_where'])){
            $i = 0;
            foreach ($filter['__like'] as $key => $value) {
                if (!empty($value)) {
                    $sql .= $i > 0 ? " AND `{$key}` LIKE '%{$value}%'" : " WHERE `{$key}` LIKE '%{$value}%'";
                    $i++;
                }
            }
        }else{
            foreach ($filter['__like'] as $key => $value) {
                if(!empty($value)) {
                    $sql .= " AND {$key} LIKE '%{$value}%'";
                }
            }
        }
    }

    $row = $wpdb->get_row($sql, ARRAY_N);

    $result['error'] = 'error';
    if ($row) $result = $row[0];

    return $result;

}

/**
 * @param string $table_name
 * @param array $id_default
 * @param array $filter
 * @param array $order_by
 * @param integer $limit
 * @param boolean $result_one
 *
 * @var integer $filter['__page']
 * @var integer $filter['__default_count_page']
 * @var array $filter['__where']
 * @var array $filter['__not_where']
 * @var array $filter['__like']
 * @var array $filter['__more']
 * @var array $filter['__less']
 *
 * @return array
 *
 * $order_by = ['id' => SORT_DESC]
 * $filter = [
 *  '__page' => 1,
 *  '__default_count_page' => 1,
 *  '__where' => ['asin' => 'adcde445566', 'upc' => '1236544587'], //равно
 *  '__not_where' => ['asin' => 'adcde445566'], // не равно
 *  '__like' => ['name' => 'abc', 'first_name' => 'cba'],
 *  '__more' => ['id' => 20, 'id_customer' => 50],  //больше
 *  '__less' => ['id' => 20, 'id_customer' => 50]  //меньше
 *
 * ]
 */
function __getResultsArray($table_name, $id_default = [], $filter = [], $order_by = [], $limit = 0, $result_one = false)
{
    global $wpdb;

    if (empty($id_default)) $id_default = ['id' => 0];

    $page = isset($filter['__page']) ? $filter['__page'] : 0;
    $default_count_page = isset($filter['__default_count_page']) ? $filter['__default_count_page'] : 0;

    if ($default_count_page > 0) {
        $offset = ($page - 1) * $default_count_page;
    }else{
        $offset = 0;
    }

    $sql = "SELECT * FROM {$table_name}";


    if (isset($filter['__more'])){
        $i = 0;
        foreach ($filter['__more'] as $key => $value){

            if ((int)$value) {
                $sql .= $i > 0 ? " AND `{$key}` > '{$value}'" : " WHERE `{$key}` > '{$value}'";
            }else{
                return $result['error'] = 'error more value';
            }
            $i++;
        }
    }

    if (isset($filter['__less'])){
        if (!isset($filter['__more'])) {
            $i = 0;
            foreach ($filter['__less'] as $key => $value) {
                if ((int)$value) {
                    $sql .= $i > 0 ? " AND `{$key}` < '{$value}'" : " WHERE `{$key}` < '{$value}'";
                } else {
                    return $result['error'] = 'error more value';
                }
                $i++;
            }
        }else{
            foreach ($filter['__less'] as $key => $value) {
                if ((int)$value) {
                    $sql .= " WHERE `{$key}` < '{$value}'";
                } else {
                    return $result['error'] = 'error less value';
                }
            }
        }
    }

    if (!isset($filter['__more']) && !isset($filter['__less'])){
        foreach ($id_default as $key => $value){
            $sql .= " WHERE `{$key}` > '{$value}'";
        }
    }


    if (isset($filter['__where'])) {
        foreach ($filter['__where'] as $key => $value) {
            if ($value != '')
                $sql .= " AND `{$key}` = '{$value}'";
        }
    }

    if (isset($filter['__not_where'])) {
        foreach ($filter['__not_where'] as $key => $value) {
            $sql .= " AND `{$key}` != '{$value}'";
        }
    }

    if (isset($filter['__like'])){
        foreach ($filter['__like'] as $key => $value) {
            if (!empty($value)) {
                $sql .= " AND {$key} LIKE '%{$value}%'";
            }
        }
    }

    if (!empty($order_by)){
        foreach ($order_by as $key => $value) {
            if ($value == SORT_DESC || $value == SORT_ASC){
                switch ($value) {
                    case SORT_DESC:
                        $value = 'DESC';
                        break;
                    case SORT_ASC:
                        $value = 'ASC';
                        break;
                    default:
                        $value = 'ASC';
                        break;
                }
            }
            $sql .= " ORDER BY `{$key}` {$value}";
        }
    }

    if ($limit > 0){
        $sql .= " LIMIT {$offset}, {$limit}";
    }

    if ($result_one == true){
        $result = $wpdb->get_row($sql, ARRAY_A);
    }else{
        $result = $wpdb->get_results($sql, ARRAY_A);
    }


    return $result;
}



/**
 * @param string $table_name
 * @param array $values
 * @return bool
 *
 * $value = ['description' => 'title', 'uom_price' => '30', 'manufacturer' => 'MAJOR']
 */

function __insertSetTable($table_name, $values = [])
{
    global $wpdb;

    $sql = "INSERT INTO {$table_name} SET";
    $i = 0;
    foreach ($values as $key => $value){
        $sql .= $i > 0 ? ", `{$key}` = '{$value}'" : " `{$key}` = '{$value}'";

        $i++;
    }
    if ($wpdb->query($sql)){
        return $wpdb->insert_id;
    }else{
        return false;
    }

}


/**
 * @param string $table_name
 * @param array $arr
 * @param bool $date
 * @return bool
 */
function __insertDataToTable($table_name, $arr, $date = false)
{
    global $wpdb;

    $i = 1;
    $count = count($arr);
    $t = '';
    $v = '';
    foreach ($arr as $table => $value){

        $value = clearString($value);

        if ($i == 1) $t .= "(";
        $t .= $i == 1 ? "`{$table}`" : ", `{$table}`";
        if ($i == $count) $t .= ")";

        if ($i == 1) $v .= "(";
        if ($table == $date){
            $v .= $i == 1 ? "{$value}" : ", {$value}";
        }else{
            $v .= $i == 1 ? "'{$value}'" : ", '{$value}'";
        }
        if ($i == $count) $v .= ")";
        $i++;
    }
    $query = "INSERT INTO {$table_name} {$t} VALUES {$v}";

    if (!empty($query))
        $wpdb->query($query);

    return true;
}



/**
 * @param string $table_name
 * @param array $keys
 * @param array $values
 * @return bool
 *
 * $key = ['id' => 20, uom => 'BX']
 * $value = ['description' => 'title', 'uom_price' => '30', 'manufacturer' => 'MAJOR']
 */

function __editTable($table_name, $keys = ['id' => 1], $values = [])
{
    if (empty($values)) return false;
    
    global $wpdb;

    $sql = "UPDATE {$table_name} SET ";
    $i = 0;
    foreach ($values as $key => $value){

        if ($value == 'NOW()'){
            $sql .= $i > 0 ? ", `{$key}` = NOW()" : " `{$key}` = NOW()";
        }else{
            $sql .= $i > 0 ? ", `{$key}` = '{$value}'" : "`{$key}` = '{$value}'";
        }

        $i++;
    }
    $i = 0;
    foreach ($keys as $key => $value){
        $sql .= $i > 0 ? " AND `{$key}` = '{$value}'" : " WHERE `{$key}` = '{$value}'";
        $i++;
    }

    if ($wpdb->query($sql)){
        return true;
    }else{
        return false;
    }

}

/**
 * @param string $table_name
 * @return bool
 */
function truncateTable($table_name)
{
    global $wpdb;
    $wpdb->query("TRUNCATE TABLE {$table_name}");
    return true;
}
?>
