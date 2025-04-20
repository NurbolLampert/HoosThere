<?php

class UsersService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Look up a user by ID.
     */
    public function getUserByID($user_id) {
        // Make sure not to leak password
        $users = $this->db->query(
            "SELECT id, name, email, year, major, hometown, description
            FROM hoos_there_users WHERE id = $1;", $user_id
        );
        if (empty($users)) return null;
        else return $users[0];
    }

    /**
     * Look up a user by email.
     */
    public function getUserByEmail($email) {
        $users = $this->db->query("SELECT * FROM hoos_there_users WHERE email = $1 LIMIT 1;", $email);
        if (empty($users)) return null;
        else return $users[0];
    }

    /**
     * Create a new user account and return it.
     */
    public function createUser($name, $year, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $this->db->query(
            "INSERT INTO hoos_there_users (name, year, email, password)
            VALUES ($1, $2, $3, $4)",
            $name, $year, $email, $hashed_password
        );
        // Fetch new user (will exist)
        return $this->getUserByEmail($email);
    }

    /**
     * Delete a user account.
     */
    public function deleteUser($user_id) {
        $this->db->query("DELETE FROM hoos_there_users WHERE id = $1;", $user_id);
    }

    /**
     * Update the current user's profile details.
     */
    public function updateUserProfile($user_id, $major, $hometown, $description) {
        $this->db->query(
            "UPDATE hoos_there_users
            SET major = $1, hometown = $2, description = $3
            WHERE id = $4",
            $major, $hometown, $description, $user_id
        );
    }
    
    /**
     * Get the ten most recently registered users.
     */
    public function getNewUsers() {
        $users = $this->db->query("SELECT * FROM hoos_there_users ORDER BY id DESC LIMIT 10");
        return $users;
    }

    /**
     * Get the user's friends list
     */
    public function getFriendsList($user_id) {
        $friends = $this->db->query(
            "WITH friend_ids AS (
                SELECT user1_id AS id FROM hoos_there_friends WHERE user2_id = $1 UNION
                SELECT user2_id AS id FROM hoos_there_friends WHERE user1_id = $1
            )
            SELECT id, name FROM hoos_there_users WHERE hoos_there_users.id IN
                (SELECT id FROM friend_ids)",
            $user_id
        );
        return $friends;
    }
}