<?php

/**
 * Database class for PostgreSQL.
 */
class Database {

    public function __construct($config) {
        $this->dbHandle = Database::connect($config);
    }

    public function query($query, ...$args) {
        $res = pg_query_params($this->dbHandle, $query, $args);
        if ($res === false) {
            echo pg_last_error($this->dbHandle);
            return array();
        }
        return pg_fetch_all($res);
    }

    public static function connect($config) {
        $host = $config["host"];
        $user = $config["user"];
        $dbname = $config["dbname"];
        $password = $config["password"];
        $port = $config["port"];

        // Connect to database
        return pg_connect(
            "host=$host port=$port dbname=$dbname user=$user password=$password"
        );
    }
    
}