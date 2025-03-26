<?php

require_once("Config.php");
require_once("Database.php");

$db = new Database();

$db->query("DROP TABLE IF EXISTS hw6_games CASCADE;");
$db->query("DROP TABLE IF EXISTS hw6_users CASCADE;");
$db->query("DROP TABLE IF EXISTS hw6_words CASCADE;");

$res = $db->query("
  CREATE TABLE hw6_users (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL
  );
");
if ($res === false) {
    die("Error creating hw6_users: " . pg_last_error());
} else {
    echo "<p>Created table hw6_users</p>";
}

$res = $db->query("
  CREATE TABLE hw6_games (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES hw6_users(id),
    score INT DEFAULT 0,
    won BOOLEAN DEFAULT FALSE,
    date_started TIMESTAMP DEFAULT NOW(),
    date_finished TIMESTAMP
  );
");
if ($res === false) {
    die("Error creating hw6_games: " . pg_last_error());
} else {
    echo "<p>Created table hw6_games</p>";
}

$res = $db->query("
  CREATE TABLE hw6_words (
    word TEXT PRIMARY KEY
  );
");
if ($res === false) {
    die("Error creating hw6_words: " . pg_last_error());
} else {
    echo "<p>Created table hw6_words</p>";
}

?>
