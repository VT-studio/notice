/*Всякие полезности*/
<?php

function loadClass($class = 'model', $name = 'order')
{
    switch ($class){
        case 'model':
            $file_name = "model-{$name}.php";
            require_once $dir_model . $file_name;
            return true;
            break;
        case 'controller':
            $file_name = "controller-{$name}.php";
            require_once $dir_controller . $file_name;
            return true;
            break;
        default:
            die("error class: '{$class}'");
            break;
    }
}

function loadView($name = 'orders', $data = [])
{
    foreach ($data as $k => $p)
    {
        ${$k} = $p;
    }
    $file_name = "view-{$name}.php";
    return require_once $dir_view . $file_name;
}


/*bd*/
function constructTable($table_arr, $ai = 'id')
{
    $table = "(";
    $i = 0;
    foreach ($table_arr as $t){
        if ($t != $ai){
            $table .= $i == 0 ? "`{$t}`" : ", `{$t}`";
            $i ++;
        }else{
            $i = 0;
        }
    }
    $table .= ")";

    return $table;
}

function constructTableInsert($table_name, $arr, $date = false)
{
    $i = 1;
    $count = count($arr);
    $t = '';
    $v = '';
    foreach ($arr as $table => $value){
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

    return $query;
}

function dataAvailability($table_name, $key, $value)
{
    $query = "SELECT {$key} FROM {$table_name} WHERE {$key} = '{$value}'";
    return $query;
}



/*other function*/
function clearString($str)
{
    $str = trim($str);
    $str = preg_replace('/\s{2,}/',' ',$str);
    $str = str_replace(array("\r","\n","\r\n",PHP_EOL),'',$str);
    $str = addslashes($str);
    $str = strip_tags($str);

    return $str;
}

function getTime($format = 'd.m.Y, H:i:s')
{
    $tz = 'America/New_York';
    $timestamp = time();
    $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
    $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
    $result = $dt->format($format);

    return $result;
}
?>
