<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dbHandle = Database::connect(LocalConfig::$db);

if ($dbHandle) {
    echo "Success connecting to database<br>\n";
} else {
    die("An error occurred connecting to the database");
}

include($include_path . "/setup_database_users.php");
include($include_path . "/setup_database_socials.php");
include($include_path . "/setup_database_academics.php");

echo "Success setting up database<br>\n";