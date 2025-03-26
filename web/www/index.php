<?php
// Published version:
// https://cs4640.cs.virginia.edu/cqq7gs/hw5/

if (headers_sent()) {
    die("Error: Headers already sent. Cannot redirect.");
}

// DEBUGGING ONLY! Show all errors.
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Class autoloading by name.  All our classes will be in a directory
// that Apache does not serve publicly.  They will be in /opt/src/, which
// is our src/ directory in Docker.
spl_autoload_register(function ($classname) {
        include "/opt/src/hw6/$classname.php";
});

// CS4640 server
// public files: public_html
// private files: anything OUTSIDE of public_html
//     create a "private" next to public_html
//     include "/students/mst3k/students/mst3k/private"
//
//     if I created "private/triviagame"
//     include "/students/mst3k/students/mst3k/private/triviagame/$classname.php";:

session_start();


$input = array_merge($_GET, $_POST);

$controller = new AnagramsController($input);

$anagrams->run();
