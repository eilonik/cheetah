<?php

define('IS_SERVER', true);
header('Content-type: application/json');
require_once '..\Server.php';

if(!isset($_SERVER['PATH_INFO'])) {
    exit();
}

$path = explode('/', trim($_SERVER['PATH_INFO'],'/'));

// Handles catalog Get requests
// Expected arguments: producer, page (both are optional)
//returns a JSON of the desired page of products
if(strtoupper($path[0]) == 'GET') {
    $producer = isset($_REQUEST['producer']) ? $_REQUEST['producer'] : null;
    $page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 ? $_REQUEST['page'] : 1;
    $control = new \Data\Control("\Data\Catalog\Product");
    $catalog = $control->getByField("producer", $producer, $page);
    echo json_encode($catalog);
}