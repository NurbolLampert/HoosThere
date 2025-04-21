<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $dbHandle = Database::connect(LocalConfig::$db);

    if ($dbHandle) {
        echo "Success connecting to database<br>\n";
    } else {
        die("An error occurred connecting to the database");
    }

    $tables = [
        "volunteering_experiences", "student_organizations", "education_records",
        "professional_experiences", "social_links",
        "future_goals", "personal_projects", "academic_records",
        "hoos_there_users", "hoos_there_friends", "hoos_there_friend_requests",
        "academic_teammates", "academic_karma",
    ];

    $sequences = [
        "future_goals_seq", "personal_projects_seq", "academic_records_seq", "hoos_there_users_seq"
    ];
    
    // Drop tables
    foreach ($tables as $table) {
        $res = pg_query($dbHandle, "DROP TABLE IF EXISTS $table CASCADE;");
    }

    // Drop sequences
    foreach ($sequences as $seq) {
        $res = pg_query($dbHandle, "DROP SEQUENCE IF EXISTS $seq CASCADE;");
    }
    
    // Create sequences
    foreach ($sequences as $seq) {
        $res = pg_query($dbHandle, "CREATE SEQUENCE $seq;");
    } 

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

    $res = pg_query($dbHandle, "CREATE TABLE hoos_there_friends (
        user1_id INT NOT NULL
            REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        user2_id INT NOT NULL
            REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        PRIMARY KEY (user1_id, user2_id),
        CHECK (user1_id < user2_id)
    );");

    $res = pg_query($dbHandle, "CREATE TABLE hoos_there_friend_requests (
        id         SERIAL PRIMARY KEY,
        from_user  INT  REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        to_user    INT  REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        created_at TIMESTAMPTZ DEFAULT NOW(),
        status     TEXT CHECK (status IN ('pending','accepted','declined')) DEFAULT 'pending',
        UNIQUE (from_user, to_user)
    );");

    $res = pg_query($dbHandle, "CREATE TABLE academic_records (
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

    $res = pg_query($dbHandle, "CREATE TABLE personal_projects (
        id INT PRIMARY KEY DEFAULT NEXTVAL('personal_projects_seq'),
        user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        project_title TEXT NOT NULL,
        description TEXT NOT NULL
    );");

    $res = pg_query($dbHandle, "CREATE TABLE future_goals (
        id INT PRIMARY KEY DEFAULT NEXTVAL('future_goals_seq'),
        user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        goal_description TEXT NOT NULL
    );");
    
    $res = pg_query($dbHandle, "CREATE TABLE social_links (
        user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        instagram TEXT DEFAULT '',
        linkedin TEXT DEFAULT '',
        facebook TEXT DEFAULT ''
    );");
    
    $res = pg_query($dbHandle, "CREATE TABLE professional_experiences (
        id SERIAL PRIMARY KEY,
        user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        role TEXT NOT NULL,
        description TEXT NOT NULL
    );");
    
    $res = pg_query($dbHandle, "CREATE TABLE education_records (
        id SERIAL PRIMARY KEY,
        user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        degree TEXT NOT NULL,
        institution TEXT NOT NULL,
        expected_graduation TEXT
    );");
    
    $res = pg_query($dbHandle, "CREATE TABLE student_organizations (
        id SERIAL PRIMARY KEY,
        user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        name TEXT NOT NULL,
        role TEXT,
        year TEXT
    );");
    
    $res = pg_query($dbHandle, "CREATE TABLE volunteering_experiences (
        id SERIAL PRIMARY KEY,
        user_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        organization TEXT NOT NULL,
        description TEXT
    );");

    $res = pg_query($dbHandle, "CREATE TABLE academic_teammates (
        record_id INT REFERENCES academic_records(id) ON DELETE CASCADE,
        teammate_id INT REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        PRIMARY KEY (record_id, teammate_id)
    );");

    $res = pg_query($dbHandle, "CREATE TABLE academic_karma (
        record_id INT  REFERENCES academic_records(id) ON DELETE CASCADE,
        rater_id  INT  REFERENCES hoos_there_users(id) ON DELETE CASCADE,
        points    INT  CHECK (points BETWEEN 0 AND 10),
        PRIMARY KEY (record_id, rater_id)
    );");

    echo "Success setting up database<br>\n";

    // Insert demo users

    $users = [
        [
            "name" => "Demo User",
            "year" => "2026",
            "email" => "demo@virginia.edu",
            "raw_password" => "demouser123!",
        ],
        [
            "name" => "Foo User",
            "year" => "2028",
            "email" => "foo@virginia.edu",
            "raw_password" => "foobar123!",
        ],
        [
            "name" => "Bar User",
            "year" => "2024",
            "email" => "bar@virginia.edu",
            "raw_password" => "barfoo123!",
        ]
    ];

    foreach ($users as $user) {
        $name = $user["name"];
        $year = $user["year"];
        $email = $user["email"];
        $rawPassword = $user["raw_password"];
        $hash = password_hash($rawPassword, PASSWORD_DEFAULT);

        $checkExisting = pg_query_params($dbHandle, "SELECT * FROM hoos_there_users WHERE email = $1;", [$email]);
        if (pg_num_rows($checkExisting) === 0) {
            pg_query_params($dbHandle, "INSERT INTO hoos_there_users (name, year, email, password) VALUES ($1, $2, $3, $4);",
                [$name, $year, $email, $hash]
            );
            echo "Inserted demo user<br>\n";
        } else {
            echo "Demo user already exists, skipping insert<br>\n";
        }
        echo "$name, $year, $email, $rawPassword<br>\n";
    }

    // Friends
    $res = pg_query($dbHandle, "INSERT INTO hoos_there_friends
        (user1_id, user2_id) VALUES (1, 2);");
    $res = pg_query($dbHandle, "INSERT INTO hoos_there_friends
        (user1_id, user2_id) VALUES (1, 3);");
    $res = pg_query($dbHandle, "INSERT INTO hoos_there_friends
        (user1_id, user2_id) VALUES (2, 3);");

    // Look up user_id for demo@virginia.edu
    $res = pg_query_params($dbHandle, "SELECT id FROM hoos_there_users WHERE email = $1;", ['demo@virginia.edu']);
    $demoUserId = pg_fetch_result($res, 0, 'id');

    $records = [
        [1, 'Fall', 'ECON 2010', 'Microeconomics', 'Aaron', 'ECON Group Project', 8],
        [1, 'Fall', 'ENWR 1505', 'Writing & Critical Inquiry', '', '', 0],
        [2, 'J-Term', 'STAT 1100', 'Statistics in Everyday Life', '', '', 0],
        [3, 'Spring', 'ECON 2020', 'Macroeconomics', 'Caroline', 'Macroeconomics Presentation', 9],
        [4, 'Spring', 'MATH 1210', 'Applied Calculus I', '', '', 0]
    ];
    

    // Academics
    foreach ($records as $r) {
        pg_query_params($dbHandle, "INSERT INTO academic_records (user_id, year, term, course_code, course_name, teammate_name, project_title, karma)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8);",
            array_merge([$demoUserId], $r)
        );
    }

    echo "Inserted sample academic records for demo@virginia.edu<br>\n";

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
    

    $goals = [
        ['Looking to pursue a career in consulting with a focus on financial markets.']
    ];
    
    foreach ($goals as $g) {
        pg_query_params($dbHandle, "INSERT INTO future_goals (user_id, goal_description)
            VALUES ($1, $2);",
            array_merge([$demoUserId], $g)
        );
    }

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

    echo "Inserted sample data for demo user<br>\n";
