<?php

$tables = [
    "academic_records", "personal_projects", "future_goals",
    "academic_teammates", "academic_karma"
];

$sequences = [
    "academic_records_seq", "personal_projects_seq"
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
pg_query($dbHandle, "CREATE TABLE academic_records (
    id INT PRIMARY KEY DEFAULT NEXTVAL('academic_records_seq'),
    user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    year INT NOT NULL,
    term TEXT NOT NULL CHECK (term IN ('Fall', 'J-Term', 'Spring', 'Summer')),
    course_code TEXT NOT NULL,
    course_name TEXT NOT NULL,
    teammate_name TEXT,
    project_title TEXT,
    karma INT DEFAULT NULL
);");

pg_query($dbHandle, "CREATE TABLE personal_projects (
    id INT PRIMARY KEY DEFAULT NEXTVAL('personal_projects_seq'),
    user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    project_title TEXT NOT NULL,
    description TEXT NOT NULL
);");

pg_query($dbHandle, "CREATE TABLE future_goals (
    id INT PRIMARY KEY DEFAULT NEXTVAL('future_goals_seq'),
    user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    goal_description TEXT NOT NULL
);");

pg_query($dbHandle, "CREATE TABLE academic_teammates (
    record_id INT REFERENCES academic_records(id) ON DELETE CASCADE,
    teammate_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    PRIMARY KEY (record_id, teammate_id)
);");

pg_query($dbHandle, "CREATE TABLE academic_karma (
    record_id INT  REFERENCES academic_records(id) ON DELETE CASCADE,
    rater_id  INT  REFERENCES hoos_there_users(id) ON DELETE CASCADE,
    points    INT  CHECK (points BETWEEN 0 AND 10),
    PRIMARY KEY (record_id, rater_id)
);");

echo "Created academic tables<br>\n";

// Insert sample academic data
$res = pg_query_params($dbHandle, "SELECT id FROM hoos_there_users WHERE email = $1;", ['demo@virginia.edu']);
$demoUserId = pg_fetch_result($res, 0, 'id');

// Records
$records = [
    [1, 'Fall', 'ECON 2010', 'Microeconomics', 'Aaron', 'ECON Group Project', 8],
    [1, 'Fall', 'ENWR 1505', 'Writing & Critical Inquiry', '', '', 0],
    [2, 'J-Term', 'STAT 1100', 'Statistics in Everyday Life', '', '', 0],
    [3, 'Spring', 'ECON 2020', 'Macroeconomics', 'Caroline', 'Macroeconomics Presentation', 9],
    [4, 'Spring', 'MATH 1210', 'Applied Calculus I', '', '', 0]
];

foreach ($records as $r) {
    pg_query_params($dbHandle, "INSERT INTO academic_records (user_id, year, term, course_code, course_name, teammate_name, project_title, karma)
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8);",
        array_merge([$demoUserId], $r)
    );
}

// Projects
$projects = [
    ['Stock Market Analysis with Python', 'Analyzed stock market trends using Python libraries.'],
    ['UVA Basketball Analytics', 'Performed statistical analysis on UVA basketball team performance.']
];

foreach ($projects as $p) {
    pg_query_params($dbHandle, "INSERT INTO personal_projects (user_id, project_title, description)
        VALUES ($1, $2, $3);",
        array_merge([$demoUserId], $p)
    );
}

// Goals
$goals = [
    ['Looking to pursue a career in consulting with a focus on financial markets.']
];

foreach ($goals as $g) {
    pg_query_params($dbHandle, "INSERT INTO future_goals (user_id, goal_description)
        VALUES ($1, $2);",
        array_merge([$demoUserId], $g)
    );
}

echo "Inserted sample academic data<br>\n";