<?php

$date1 = '2016-02-28';
$date2 = '05/08/2016';


function valid_date($string){
    $date = explode('-', $string);
    $date_firefox = explode('/', $string);
    if(isset($date[0]) && isset($date[1]) && isset($date[2])){
        $valid_date = checkdate($date[1], $date[2], $date[0]);
        if($valid_date){
            return $string;
        } else{
            return false;
        }
    } elseif(isset($date_firefox[0]) && isset($date_firefox[1]) && isset($date_firefox[2])){
        $valid_date_firefox = checkdate($date_firefox[0], $date_firefox[1], $date_firefox[2]);
        if($valid_date_firefox){
            return $date_firefox[2]. '-' . $date_firefox[0] . '-' . $date_firefox[1];
        } else {
            return false;
        }
    } else {
        return false;
    }
}

var_dump(valid_date($date2));