<?php

// Socials and professionals

$tables = [
    "social_links",
    "professional_experiences", "education_records",
    "volunteering_experiences", "student_organizations"
];

$sequences = [
    "future_goals_seq"
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
    pg_query($dbHandle, "CREATE SEQUENCE $seq;");
}

// Create tables
pg_query($dbHandle, "CREATE TABLE social_links (
    user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    instagram TEXT DEFAULT '',
    linkedin TEXT DEFAULT '',
    facebook TEXT DEFAULT ''
);");

pg_query($dbHandle, "CREATE TABLE professional_experiences (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    role TEXT NOT NULL,
    description TEXT NOT NULL
);");

pg_query($dbHandle, "CREATE TABLE education_records (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    degree TEXT NOT NULL,
    institution TEXT NOT NULL,
    expected_graduation TEXT
);");

pg_query($dbHandle, "CREATE TABLE student_organizations (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    name TEXT NOT NULL,
    role TEXT,
    year TEXT
);");

pg_query($dbHandle, "CREATE TABLE volunteering_experiences (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    organization TEXT NOT NULL,
    description TEXT
);");

echo "Created social tables<br>\n";

// Insert sample social data
$res = pg_query_params($dbHandle, "SELECT id FROM hoos_there_users WHERE email = $1;", ['demo@virginia.edu']);
$demoUserId = pg_fetch_result($res, 0, 'id');

// Links
pg_query_params($dbHandle, "INSERT INTO social_links (user_id, instagram, linkedin, facebook)
VALUES ($1, $2, $3, $4);", [
    $demoUserId,
    'https://instagram.com/chadwahoo',
    'https://linkedin.com/in/chadwahoo',
    'https://facebook.com/chadwahoo'
]);

// Experiences
$experiences = [
    ['Intern at XYZ Consulting', 'Worked on client research and analysis'],
    ['Part-time Research Assistant', 'UVA Economics Department support role']
];
foreach ($experiences as $e) {
    pg_query_params($dbHandle, "INSERT INTO professional_experiences (user_id, role, description)
        VALUES ($1, $2, $3);", array_merge([$demoUserId], $e));
}

// Education
pg_query_params($dbHandle, "INSERT INTO education_records (user_id, degree, institution, expected_graduation)
    VALUES ($1, $2, $3, $4);", [
        $demoUserId,
        'B.A. in Economics, Minor in Statistics',
        'University of Virginia',
        'May 2025'
    ]);

// Clubs
$clubs = [
    ['Comics Club', 'Vice President', '2024'],
    ['Albanian Cooking Club', 'Treasurer', '2023'],
    ['Rock-apella Group', 'Member', '2022-present']
];
foreach ($clubs as $c) {
    pg_query_params($dbHandle, "INSERT INTO student_organizations (user_id, name, role, year)
        VALUES ($1, $2, $3, $4);", array_merge([$demoUserId], $c));
}

// Volunteering
$vols = [
    ['Habitat for Humanity', '2024 Build Project'],
    ['Local Food Drive', 'Coordinator for local donations']
];
foreach ($vols as $v) {
    pg_query_params($dbHandle, "INSERT INTO volunteering_experiences (user_id, organization, description)
        VALUES ($1, $2, $3);", array_merge([$demoUserId], $v));
}

echo "Inserted sample social data<br>\n";