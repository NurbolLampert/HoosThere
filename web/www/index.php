<?php
// For debugging: show errors
error_reporting(E_ALL);
ini_set("display_errors", 1);

// If CS 4640 server instead of Docker environment
$is_remote = false;
if ($is_remote) {
    $include_path = "/students/qgt7zm/students/qgt7zm/private";
} else {
    $include_path = "/opt/src";
}
$include_path .= "/hoos_there";

// Class autoloading
spl_autoload_register(function ($classname) use ($include_path) {
    include "$include_path/$classname.php";
});

// Run the database script
$reset_db = false;
if ($reset_db) {
    include($include_path . "/setup_database.php");
    exit();
}

// Run the controller
$controller = new HoosThereController($_GET, $include_path, $is_remote);
$controller->run();