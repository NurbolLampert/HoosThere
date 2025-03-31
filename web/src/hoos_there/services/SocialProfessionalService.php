<?php

class SocialProfessionalService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getSocialProfessionalData($user_id) {
        $experiences = $this->db->query("SELECT id, role, description FROM professional_experiences WHERE user_id = $1 ORDER BY id;", $user_id);
        $education = $this->db->query("SELECT id, degree, institution, expected_graduation FROM education_records WHERE user_id = $1 ORDER BY id;", $user_id);
        $clubs = $this->db->query("SELECT id, name, role, year FROM student_organizations WHERE user_id = $1 ORDER BY id;", $user_id);
        $volunteering = $this->db->query("SELECT id, organization, description FROM volunteering_experiences WHERE user_id = $1 ORDER BY id;", $user_id);
        $social = $this->db->query("SELECT instagram, linkedin, facebook FROM social_links WHERE user_id = $1 LIMIT 1;", $user_id);
        return [
            "experiences" => $experiences,
            "education" => $education,
            "clubs" => $clubs,
            "volunteering" => $volunteering,
            "social" => $social[0] ?? []
        ];
    }

    public function updateSocialLinks($user_id, $insta, $linkedin, $facebook) {
        $existing = $this->db->query("SELECT * FROM social_links WHERE user_id = $1;", $user_id);
        if (empty($existing)) {
            $this->db->query(
                "INSERT INTO social_links (user_id, instagram, linkedin, facebook) VALUES ($1, $2, $3, $4);",
                $user_id, $insta, $linkedin, $facebook
            );
        } else {
            $this->db->query(
                "UPDATE social_links SET instagram = $1, linkedin = $2, facebook = $3 WHERE user_id = $4;",
                $insta, $linkedin, $facebook, $user_id
            );
        }
    }

    public function addExperience($user_id, $role, $description) {
        $this->db->query(
            "INSERT INTO professional_experiences (user_id, role, description) VALUES ($1, $2, $3);",
            $user_id, $role, $description
        );
    }

    public function updateExperience($id, $role, $description) {
        $this->db->query("UPDATE professional_experiences SET role = $1, description = $2 WHERE id = $3;",
            $role, $description, $id
        );
    }

    public function addEducation($user_id, $degree, $institution, $expected_graduation) {
        $this->db->query("INSERT INTO education_records (user_id, degree, institution, expected_graduation) VALUES ($1, $2, $3, $4);",
            $user_id, $degree, $institution, $expected_graduation
        );
    }

    public function updateEducation($id, $degree, $institution, $expected_graduation) {
        $this->db->query(
            "UPDATE education_records SET degree = $1, institution = $2, expected_graduation = $3 WHERE id = $4;",
            $degree, $institution, $expected_graduation, $id
        );
    }

    public function addClub($user_id, $name, $role, $year) {
        $this->db->query(
            "INSERT INTO student_organizations (user_id, name, role, year) VALUES ($1, $2, $3, $4);",
            $user_id, $name, $role, $year
        );
    }

    public function updateClub($id, $name, $role, $year) {
        $this->db->query(
            "UPDATE student_organizations SET name = $1, role = $2, year = $3 WHERE id = $4;",
            $name, $role, $year, $id
        );
    }

    public function addVolunteer($user_id, $organization, $description) {
        $this->db->query(
            "INSERT INTO volunteering_experiences (user_id, organization, description) VALUES ($1, $2, $3);",
            $user_id, $organization, $description
        );
    }

    public function updateVolunteer($id, $organization, $description) {
        $this->db->query(
            "UPDATE volunteering_experiences SET organization = $1, description = $2 WHERE id = $3;",
            $organization, $description, $id
        );
    }
}
