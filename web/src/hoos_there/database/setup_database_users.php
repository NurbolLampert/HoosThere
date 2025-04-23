<?php

// Users and friends
$tables = [
    "hoos_there_users", "hoos_there_friend_requests", "hoos_there_friends"
];

$sequences = [
    "hoos_there_users_seq"
];

// Drop tables
foreach ($tables as $table) {
    pg_query($dbHandle, "DROP TABLE IF EXISTS $table CASCADE;");
}

// Drop sequences
foreach ($sequences as $seq) {
    pg_query($dbHandle, "DROP SEQUENCE IF EXISTS $seq CASCADE;");
}

// Create sequences
foreach ($sequences as $seq) {
    pg_query($dbHandle, "CREATE SEQUENCE IF NOT EXISTS $seq;");
}

// Create tables
pg_query($dbHandle, "CREATE TABLE IF NOT EXISTS hoos_there_users (
    id INT PRIMARY KEY DEFAULT NEXTVAL('hoos_there_users_seq'),
    name TEXT NOT NULL,
    year INT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,

    major TEXT NOT NULL DEFAULT '',
    hometown TEXT NOT NULL DEFAULT '',
    description TEXT NOT NULL DEFAULT '',
    karma_avg NUMERIC(10,3) DEFAULT 0,
    karma_votes INT DEFAULT 0,
    CHECK (year > 0)
);");

pg_query($dbHandle, "CREATE TABLE IF NOT EXISTS hoos_there_friend_requests (
    id         SERIAL PRIMARY KEY,
    from_user  INT  REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    to_user    INT  REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    status     TEXT CHECK (status IN ('pending','accepted','declined')) DEFAULT 'pending',
    UNIQUE (from_user, to_user)
);");

pg_query($dbHandle, "CREATE TABLE IF NOT EXISTS hoos_there_friends (
    user1_id INT NOT NULL REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    user2_id INT NOT NULL REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    PRIMARY KEY (user1_id, user2_id),
    CHECK (user1_id < user2_id)
);");

echo "Created user tables<br>\n";

// Insert demo users
$demo_users = [
    [
        "name" => "Demo User",
        "year" => "2026",
        "email" => "demo@virginia.edu",
        "raw_password" => "demouser123!",
        "hometown" => "Demoville",
        "description" => "Hoos there is my favorite site!"
    ],
    [
        "name" => "Foo User",
        "year" => "2028",
        "email" => "foo@virginia.edu",
        "raw_password" => "foobar123!",
        "hometown" => "Footown",
    ],
    [
        "name" => "Bar User",
        "year" => "2024",
        "email" => "bar@virginia.edu",
        "raw_password" => "barfoo123!",
        "hometown" => "Barborough",
    ],
    [
        "name" => "Test User",
        "year" => "2025",
        "email" => "test@virginia.edu",
        "raw_password" => "Testtest123!",
        "hometown" => "Test City",
        "description" => "**I am a real user.**"
    ]
];

foreach ($demo_users as $user) {
    $name = $user["name"];
    $year = $user["year"];
    $email = $user["email"];
    $rawPassword = $user["raw_password"];
    $hash = password_hash($rawPassword, PASSWORD_DEFAULT);
    $hometown = $user["hometown"];
    $description = $user["description"] ?? "";
    
    $checkExisting = pg_query_params(
        $dbHandle, "SELECT * FROM hoos_there_users WHERE email = $1;", [$email]
    );
    if (pg_num_rows($checkExisting) === 0) {
        pg_query_params(
            $dbHandle,
            "INSERT INTO hoos_there_users (name, year, email, password, hometown, description)
            VALUES ($1, $2, $3, $4, $5, $6);",
            [$name, $year, $email, $hash, $hometown, $description]
        );
        echo "Inserted user $name<br>\n";
    } else {
        echo "User $name already exists, skipping insert<br>\n";
    }
    echo "$name, $year, $email, $rawPassword<br>\n";
}

// Insert sample friends
pg_query($dbHandle, "INSERT INTO hoos_there_friends
    (user1_id, user2_id) VALUES (1, 2);");
pg_query($dbHandle, "INSERT INTO hoos_there_friends
    (user1_id, user2_id) VALUES (1, 3);");
pg_query($dbHandle, "INSERT INTO hoos_there_friends
    (user1_id, user2_id) VALUES (2, 3);");

// Insert sample friend requests
pg_query($dbHandle, "INSERT INTO hoos_there_friend_requests
    (from_user, to_user) VALUES (4, 1);");