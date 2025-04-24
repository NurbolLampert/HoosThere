<?php

/* ---------- 1. USERS  ---------------------------------------------------- */
$usrSQL = "INSERT INTO hoos_there_users
             (email, password, name,
              year, major, hometown, description,
              karma_avg, karma_votes)
           VALUES ($1,$2,$3,$4,$5,$6,$7,0,0)
           ON CONFLICT (id) DO NOTHING";

// IDs 5-14
$rawUsers = [
 // email,               password,      name,            year, major, hometown, description
 ['alice@virginia.edu',       'Password1!',  'Alice Zhang',   2026,'Computer Science','Richmond, VA',
  'CS major who loves full-stack dev and coffee.'],
 ['brandon@virginia.edu',     'Password1!',  'Brandon Lee',   2025,'Economics','Fairfax, VA',
  'Aspiring consultant; debates for fun.'],
 ['carmen@virginia.edu',      'Password1!',  'Carmen Ortiz',  2026,'Statistics','Norfolk, VA',
  'Data nerd & salsa dancer.'],
 ['diego@virginia.edu',       'Password1!',  'Diego Martínez',2024,'Mechanical Eng.','Roanoke, VA',
  'Robotics team lead.'],
 ['emma@virginia.edu',        'Password1!',  'Emma Johnson',  2025,'Biology','Charlottesville, VA',
  'Pre-med researching CRISPR.'],
 ['farah@virginia.edu',       'Password1!',  'Farah Ali',     2025,'Psychology','Alexandria, VA',
  'UX enthusiast & peer counselor.'],
 ['gavin@virginia.edu',       'Password1!',  'Gavin Patel',   2026,'Computer Science','Herndon, VA',
  'Competitive programmer, coffee addict.'],
 ['hannah@virginia.edu',      'Password1!',  'Hannah Kim',    2024,'Architecture','Richmond, VA',
  'Urban design + watercolor sketches.'],
 ['ivan@virginia.edu',        'Password1!',  'Ivan Petrov',   2026,'Math','Moscow, Russia',
  'Number theory & chess club captain.'],
 ['julia@virginia.edu',       'Password1!',  'Julia Nguyen',  2025,'Commerce','Virginia Beach, VA',
  'Marketing analytics & foodie.']
];

foreach ($rawUsers as $u) {
  [$email,$plain,$name,$year,$major,$town,$desc] = $u;
  $hash = password_hash($plain,PASSWORD_DEFAULT);
  pg_query_params($dbHandle,$usrSQL,[$email,$hash,$name,$year,$major,$town,$desc]);
}

/* ---------- 2. helper to insert many rows parameterized ------------------ */
function bulk($dbHandle,$sql,$rows){ foreach($rows as $r) pg_query_params($dbHandle,$sql,$r); }

/* ---------- 3. ACADEMICS + teammates ------------------------------------ */
$acadSQL = "INSERT INTO academic_records
              (user_id,year,term,course_code,course_name,
               project_title, teammate_name)
            VALUES ($1,$2,$3,$4,$5,$6,$7) RETURNING id";
$teamSQL = "INSERT INTO academic_teammates (record_id, teammate_id)
            VALUES ($1,$2)";

for ($u=5;$u<=14;$u++){
    // Fall 2024
    $r1 = pg_fetch_row(pg_query_params($dbHandle,$acadSQL,[
           $u,4,'Fall','CS 4640','Web PL & Dev',
           'Team Portfolio','Demo User']));
    // Spring 2025
    pg_query_params($dbHandle,$acadSQL,[
           $u,3,'Spring','STAT 3120','Intro Regression',
           'Kaggle House Prices', 'Foo User']);

    // teammate = next user cyclically
    pg_query_params($dbHandle,$teamSQL,[$r1[0], ($u%10)+1]);
}

/* ---------- 4. PROFESSIONAL EXPERIENCE ---------------------------------- */
bulk($dbHandle,
 "INSERT INTO professional_experiences (user_id, role, description)
  VALUES ($1,$2,$3)",
 [
   [5,'Software Intern','Built React components for analytics.'],
   [6,'Research Assistant','Analyzed consumer survey data.'],
   [7,'Teaching Assistant','Led weekly stats lab.'],
   [8,'Lab Technician','3-D printed robot chassis.'],
   [9,'Shadow Intern','Assisted in gene-editing wet lab.'],
   [10,'UX Intern','Conducted heuristic evaluations of app.'],
   [11,'Backend Intern','Optimized SQL queries for e-commerce.'],
   [12,'Design Intern','Modelled mixed-use space in Rhino.'],
   [13,'Math Grader','Graded problem sets for Calc II.'],
  [14,'Marketing Intern','Managed social campaigns for start-up.']
 ]);

/* ---------- 5. EDUCATION ------------------------------------------------- */
$eduSQL = "INSERT INTO education_records
             (user_id, degree, institution, expected_graduation)
           VALUES ($1,$2,$3,$4)";

$majList = ['CS','Econ','Stats','ME','Bio','Psych','CS','Arch','Math','Comm'];

foreach (range(5,14) as $u){
  $idx  = ($u - 5) % 10;                   // 0..9 always in range
  $maj  = $majList[$idx];
  $grad = in_array($u,[8,12]) ? 'May 2024' : 'May 2025';
  pg_query_params($dbHandle,$eduSQL,[$u,"B.S. $maj",'University of Virginia',$grad]);
}

/* ---------- 6. CLUBS ----------------------------------------------------- */
bulk($dbHandle,
 "INSERT INTO student_organizations (user_id,name,role,year)
  VALUES ($1,$2,$3,$4)",
 [
   [5,'ACM','Treasurer',2024],
   [6,'Debate Society','Member',2024],
   [7,'Salsa Club','President',2024],
   [8,'Robotics','Lead Engineer',2023],
   [9,'Pre-Med Club','Secretary',2024],
   [10,'Peer Counselors','Volunteer',2024],
   [11,'Competitive Programming','Captain',2024],
   [12,'AIAS','Member',2023],
   [13,'Chess Club','Captain',2024],
  [14,'Marketing Assoc.','VP Outreach',2024]
 ]);

/* ---------- 7. VOLUNTEERING --------------------------------------------- */
bulk($dbHandle,
 "INSERT INTO volunteering_experiences (user_id, organization, description)
  VALUES ($1,$2,$3)",
 [
  [5,'CodeVA','Taught Scratch to middle schoolers.'],
  [6,'Food Bank','Packed weekend meal kits.'],
  [7,'Girls Who Code','Mentored high-school students.'],
  [8,'Habitat for Humanity','Built wheelchair ramp.'],
  [9,'UVA Hospital','Patient transport volunteer.'],
  [10,'Counseling Center','Peer hotline shifts.'],
  [11,'FIRST Lego League','Refereed regional competition.'],
  [12,'City Parks','Tree inventory GIS project.'],
  [13,'Local Library','Math tutoring.'],
 [14,'Animal Shelter','Socialized cats.']
 ]);

/* ---------- 8. SOCIAL LINKS --------------------------------------------- */
bulk($dbHandle,
 "INSERT INTO social_links (user_id,instagram,linkedin,facebook)
  VALUES ($1,$2,$3,$4)",
 [
  [5,'https://ig.com/alicez','https://linkedin.com/in/alicez',null],
  [6,null,'https://linkedin.com/in/brandonl','https://fb.com/brandonl'],
  [7,'https://ig.com/carmen.o',null,null],
  [8,null,'https://linkedin.com/in/diegom','https://fb.com/diegom'],
  [9,'https://ig.com/emj','https://linkedin.com/in/emmaj',null],
  [10,null,'https://linkedin.com/in/farahali',null],
  [11,'https://ig.com/gavin.codes',null,null],
  [12,'https://ig.com/hannahk','https://linkedin.com/in/hannahk','https://fb.com/hannahk'],
  [13,null,null,'https://fb.com/ivanp'],
 [14,'https://ig.com/julia.n','https://linkedin.com/in/julian',null]
 ]);

/* ---------- 9. FRIENDSHIPS & REQUESTS ----------------------------------- */

$friendPairs = [[5,6],[7,8],[7,9],[5,10],[5,14]];
$friendSQL   = "INSERT INTO hoos_there_friends (user1_id,user2_id)
                VALUES ($1,$2)
                ON CONFLICT DO NOTHING";

foreach ($friendPairs as [$a,$b]) {
    pg_query_params($dbHandle,$friendSQL,[min($a,$b), max($a,$b)]);
}

$requests = [[5,7],[8,5],[11,9],[12,6],[13,7],[10,12],[10,14]];
$reqSQL   = "INSERT INTO hoos_there_friend_requests (from_user,to_user)
             VALUES ($1,$2)
             ON CONFLICT (from_user,to_user) DO NOTHING";
foreach ($requests as $r) pg_query_params($dbHandle,$reqSQL,$r);


/* ---------- Done ---------- */
echo "Demo data inserted.<br>\n";
