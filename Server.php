<?php
require_once '\Data\Pages.php';
require_once '\Data\Catalog\Product.php';
require_once '\Data\Control.php';
if(!defined('IS_SERVER') || !IS_SERVER) {
    die('ACCESS DENIED');
}

function dbConnect() {
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $conn  = new PDO("mysql:host=$servername;dbname=cheetah", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}
