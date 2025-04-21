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
     * Look up a user by name.
     */
    public function getUserByName($name) {
        // Possible that name is not unique
        $users = $this->db->query(
            "SELECT id, name, email, year, major, hometown, description
            FROM hoos_there_users WHERE name = $1 LIMIT 1;", $name
        );
        if (empty($users)) return null;
        else return $users[0];
    }

    /**
     * Look up a user by email.
     */
    public function getUserByEmail($email) {
        $users = $this->db->query(
            // Need to compare password
            "SELECT id, name, email, password FROM hoos_there_users WHERE email = $1 LIMIT 1;", $email
        );
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
     * Get the most recently registered users.
     */
    public function getNewUsers($count = 10) {
        $users = $this->db->query("SELECT * FROM hoos_there_users ORDER BY id DESC LIMIT $count");
        return $users;
    }

    /**
     * Get the logged-in user's friends list.
     */
    public function getFriendsList($user_id) {
        $friends = $this->db->query(
            "WITH friend_ids AS (
                SELECT user1_id AS id FROM hoos_there_friends WHERE user2_id = $1 UNION
                SELECT user2_id AS id FROM hoos_there_friends WHERE user1_id = $1
            )
            SELECT id, name, year, major
            FROM hoos_there_users
            WHERE id IN (SELECT id FROM friend_ids)
            ORDER BY name",
            $user_id
        );
        return $friends;
    }

    /**
     * Check if the two users are friends with each other.
     */
    public function areUsersFriends($user_id, $friend_id) {
        $user1_id = min($user_id, $friend_id);
        $user2_id = max($user_id, $friend_id);
        return $this->db->query("SELECT * FROM hoos_there_friends
            WHERE user1_id = $1 AND user2_id = $2 LIMIT 1;",
            $user1_id, $user2_id);
    }

    /**
     * Add the two users to each others' friend lists.
     */
    public function addFriends($user_id, $friend_id) {
        $user1_id = min($user_id, $friend_id);
        $user2_id = max($user_id, $friend_id);
        $this->db->query("INSERT INTO hoos_there_friends
            (user1_id, user2_id) VALUES ($1, $2);",
            $user1_id, $user2_id);
    }

    /**
     * Remove the two users from each others' friend lists.
     */
    public function removeFriends($user_id, $friend_id) {
        $user1_id = min($user_id, $friend_id);
        $user2_id = max($user_id, $friend_id);
        $this->db->query("DELETE FROM hoos_there_friends
            WHERE user1_id = $1 AND user2_id = $2;",
            $user1_id, $user2_id);
    }

    /**
     * Get the user's mutual friends list with the logged-in user.
     */
    public function getMutualFriendsList($user_id, $other_id) {
        $friends = $this->db->query(
            "WITH user_friend_ids AS (
                SELECT user1_id AS id FROM hoos_there_friends WHERE user2_id = $1 UNION
                SELECT user2_id AS id FROM hoos_there_friends WHERE user1_id = $1
            ),
            other_friend_ids AS (
                SELECT user1_id AS id FROM hoos_there_friends WHERE user2_id = $2 UNION
                SELECT user2_id AS id FROM hoos_there_friends WHERE user1_id = $2
            )
            SELECT id, name FROM hoos_there_users WHERE hoos_there_users.id
                IN (SELECT id FROM user_friend_ids INTERSECT
                    SELECT id FROM other_friend_ids)
                ORDER BY name",
            $user_id, $other_id
        );
        return $friends;
    }

    public function searchUsers($userId, $term, $limit = 15) {
        $sql = "WITH friend_ids AS (
                SELECT user1_id AS id FROM hoos_there_friends WHERE user2_id = $1
                UNION
                SELECT user2_id AS id FROM hoos_there_friends WHERE user1_id = $1
            )
            SELECT u.id, u.name,
                (u.id = ANY(SELECT id FROM friend_ids)) AS is_friend
            FROM hoos_there_users u
            WHERE u.name ILIKE '%' || $2 || '%'
            AND u.id <> $1
            ORDER BY u.name
            LIMIT $3";
        return $this->db->query($sql, $userId, $term, $limit);
    }
    
    public function createFriendRequest($from, $to) {
        $this->db->query(
          "INSERT INTO hoos_there_friend_requests (from_user, to_user)
           VALUES ($1, $2) ON CONFLICT DO NOTHING",
          $from, $to
        );
    }
    
    public function getIncomingRequests($userId) {
        return $this->db->query(
          "SELECT r.id, r.from_user, u.name, r.created_at
             FROM hoos_there_friend_requests r
             JOIN hoos_there_users u ON u.id = r.from_user
            WHERE r.to_user = $1 AND r.status = 'pending'
            ORDER BY r.created_at DESC",
          $userId
        );
    }
    
    public function actOnRequest($reqId, $accept) {
        $status = $accept ? 'accepted' : 'declined';
        $this->db->query(
          "UPDATE hoos_there_friend_requests SET status = $1 WHERE id = $2",
          $status, $reqId
        );
        if ($accept) {
            // fetch ids to add to friends list
            $rows = $this->db->query(
              "SELECT from_user, to_user FROM hoos_there_friend_requests WHERE id = $1",
              $reqId
            );
            if ($rows) $this->addFriends($rows[0]["from_user"], $rows[0]["to_user"]);
        }
    }
    
}