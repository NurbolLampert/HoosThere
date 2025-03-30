<?php
    $dbHandle = Database::connect(LocalConfig::$db);

    if ($dbHandle) {
        echo "Success connecting to database<br>\n";
    } else {
        die("An error occurred connecting to the database");
    }

    // Drop tables and sequences
    $res = pg_query($dbHandle, "DROP TABLE IF EXISTS hoos_there_users;");
    $res = pg_query($dbHandle, "DROP SEQUENCE IF EXISTS hoos_there_users_seq;");

    // Create tables and sequences
    $res = pg_query($dbHandle, "CREATE SEQUENCE hoos_there_users_seq;");
    $res = pg_query($dbHandle, "CREATE TABLE hoos_there_users (
        id INT PRIMARY KEY DEFAULT NEXTVAL('hoos_there_users_seq'),
        name TEXT NOT NULL,
        year INT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,

        major TEXT NOT NULL DEFAULT '',
        hometown TEXT NOT NULL DEFAULT '',
        description TEXT NOT NULL DEFAULT '',
        CHECK (year > 0)
        
    );");

    echo "Success setting up database<br>\n";