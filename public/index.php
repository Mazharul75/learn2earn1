<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/config.php";

// LOAD THESE FIRST
require_once "../app/core/Database.php";
require_once "../app/core/Model.php";
require_once "../app/core/Controller.php";
require_once "../app/core/App.php";

// START THE APP LAST
$app = new App();