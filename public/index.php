<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/config.php";
require_once "../app/core/App.php";
require_once "../app/core/Controller.php";
require_once "../app/core/Model.php";
require_once "../app/core/Database.php";

$db = new Database();
echo "Database Connected Successfully";
?>