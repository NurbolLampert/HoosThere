<?php

class AcademicsService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getRecords($user_id) {
        $result = $this->db->query("SELECT * FROM academic_records WHERE user_id = $1", $user_id);

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

    public function insertRecord($user_id, $year, $term, $code, $name, $teammate, $project, $karma) {
        $this->db->query(
            "INSERT INTO academic_records (user_id, year, term, course_code, course_name, teammate_name, project_title, karma)
             VALUES ($1, $2, $3, $4, $5, $6, $7, $8)",
            $user_id, $year, $term, $code, $name, $teammate, $project, $karma
        );
    }


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

    public function updateGoal($user_id, $goal_text) {
        $this->db->query("DELETE FROM future_goals WHERE user_id = $1", $user_id); // allow only one for now
        $this->db->query("INSERT INTO future_goals (user_id, goal_description) VALUES ($1, $2)", $user_id, $goal_text);
    }
}
