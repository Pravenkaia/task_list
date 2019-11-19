<?php

use models\DB;

include_once 'autoloader.php';

try{
	DB::getInstance();
}
catch (PDOException $e){
    echo "DB is not available";
    var_dump($e->getTrace());
}
catch (Exception $e){
    echo $e->getMessage();
}
