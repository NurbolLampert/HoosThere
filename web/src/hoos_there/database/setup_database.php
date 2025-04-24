<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Switch to RemoteConfig class when deploying
$dbHandle = Database::connect(LocalConfig::$db);

if ($dbHandle) {
    echo "Success connecting to database<br>\n";
} else {
    die("An error occurred connecting to the database");
}

include($include_path . "/database/setup_database_users.php");
include($include_path . "/database/setup_database_socials.php");
include($include_path . "/database/setup_database_academics.php");
include($include_path . "/database/setup_database_samples.php");

echo "Success setting up database<br>\n";