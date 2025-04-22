<?php

class AcademicsService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Records

    public function getRecords($user_id) {
        $result = $this->db->query("SELECT ar.*, 
                    COALESCE(k.avg,0) AS karma_avg, 
                    COALESCE(k.n,0)   AS karma_votes
            FROM academic_records ar
            LEFT JOIN (
                SELECT record_id, AVG(points)::numeric(10,3) AS avg, COUNT(*) AS n
                FROM academic_karma GROUP BY record_id
            ) k ON k.record_id = ar.id
            WHERE ar.user_id = $1
            ORDER BY year DESC, term DESC", $user_id);
        $grouped = [];
        foreach ($result as $r) {
            $grouped[$r["year"]][$r["term"]][] = $r;
        }

        $projects = $this->db->query("SELECT * FROM personal_projects WHERE user_id = $1", $user_id);
        $goals = $this->db->query("SELECT * FROM future_goals WHERE user_id = $1", $user_id);
    

        $user = $this->db->query("SELECT * FROM hoos_there_users WHERE id = $1", $user_id);
        return [
            "grouped" => $grouped,
            "projects" => $projects,
            "goals" => $goals
        ];
    }

    public function updateRecord($id, $course_code, $course_name, $teammate, $project, $karma) {
        $this->db->query(
            "UPDATE academic_records
             SET course_code = $1, course_name = $2, teammate_name = $3, project_title = $4, karma = $5
             WHERE id = $6",
            $course_code, $course_name, $teammate, $project, $karma, $id
        );
    }

    public function addRecord($user_id, $year, $term, $code, $name, $teammate, $project, $karma) {
        $res[0] = $this->db->query(
            "INSERT INTO academic_records
            (user_id, year, term, course_code, course_name, teammate_name, project_title, karma)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
            RETURNING id",
            $user_id, $year, $term, $code, $name, $teammate, $project, $karma
        );
        return $res[0][0]["id"]; // Fetch new record id
    }

    public function deleteRecord($id) {
        $this->db->query("DELETE FROM academic_records WHERE id = $1", $id);
    }

    // Projects

    public function updateProject($user_id, $project_id, $title, $desc) {
        $this->db->query(
            "UPDATE personal_projects
             SET project_title = $1, description = $2
             WHERE id = $3 AND user_id = $4",
            $title, $desc, $project_id, $user_id
        );
    }

    public function addProject($user_id, $title, $desc) {
        $this->db->query("INSERT INTO personal_projects (user_id, project_title, description) VALUES ($1, $2, $3)",
            $user_id, $title, $desc
        );
    }

    public function deleteProject($id) {
        $this->db->query("DELETE FROM academic_records WHERE id = $1", $id);
    }

    // Goals

    public function updateGoal($user_id, $goal_text) {
        $this->db->query("DELETE FROM future_goals WHERE user_id = $1", $user_id); // allow only one for now
        $this->db->query("INSERT INTO future_goals (user_id, goal_description) VALUES ($1, $2)", $user_id, $goal_text);
    }

    // Teammates

    public function addTeammates($recordId, array $teammateIds) {
        foreach ($teammateIds as $uid) {
            $this->db->query(
                "INSERT INTO academic_teammates (record_id, teammate_id)
                VALUES ($1, $2) ON CONFLICT DO NOTHING",
                $recordId, $uid
            );
        }
        $res = $this->db->query(
            "SELECT * FROM academic_teammates"
        );
    }

    public function getTeammates($recordId){
        return $this->db->query(
            "SELECT u.id, u.name
            FROM academic_teammates t
            JOIN hoos_there_users u ON u.id = t.teammate_id
            WHERE t.record_id = $1
            ORDER BY u.name", $recordId
        );
    }
    

    public function userIsTeammate($recordId, $userId) {
        $res = $this->db->query(
            "SELECT * FROM academic_teammates
            WHERE record_id = $1 AND teammate_id = $2 LIMIT 1",
            $recordId, $userId
        );
        return !empty($this->db->query(
            "SELECT 1 FROM academic_teammates
            WHERE record_id = $1 AND teammate_id = $2 LIMIT 1",
            $recordId, $userId
        ));
    }

    // Karma

    public function saveKarma($recordId, $raterId, $points) {
        $this->db->query(
            "INSERT INTO academic_karma (record_id, rater_id, points)
            VALUES ($1,$2,$3)
            ON CONFLICT (record_id, rater_id)
            DO UPDATE SET points = $3",
            $recordId, $raterId, $points
        );
    }

    public function getKarmaSummary($recordId, $viewerId=null){
        $base = $this->db->query(
            "SELECT COUNT(*) AS n, COALESCE(AVG(points),0)::numeric(10,3) AS avg
               FROM academic_karma WHERE record_id = $1", $recordId)[0];
        if ($viewerId){
            $mine = $this->db->query(
                "SELECT points FROM academic_karma WHERE record_id=$1 AND rater_id=$2 LIMIT 1",
                $recordId, $viewerId);
            $base["my"] = $mine ? $mine[0]["points"] : null;
        }
        return $base;
    }
    
}
