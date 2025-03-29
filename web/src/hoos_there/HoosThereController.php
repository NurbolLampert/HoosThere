<?php

/**
 * Front controller for HoosThere app.
 */
class HoosThereController {

    public function __construct($input, $include_path, $is_remote) {
        $this->input = $input;
        $this->include_path = $include_path;
        if ($is_remote) {
            $this->db = new Database(RemoteConfig::$db);
        } else {
            $this->db = new Database(LocalConfig::$db);
        }
        session_start();
    }

    public function run() {
        if (isset($this->input["command"])) {
            $command = $this->input["command"];
        } else {
            $command = null;
        }

        switch($command) {
            case "login":
                $this->login();
                break;
            case "home":
            default:
            $this->showTemplate("/templates/home.php");
                break;
        }
    }

    private function login() {
        // Validate form is not empty
        if (
            !isset($_POST["email"]) || empty($_POST["email"]) ||
            !isset($_POST["password"]) || empty($_POST["password"])
        ) {
            $this->createAlert("Please provide an email and password.", "danger");
            $this->redirectPage("home");
            return;
        }

        // Validate email
        $email = $_POST["email"];
        if (!preg_match("/^\w+@virginia.edu$/", $email)) {
            $this->createAlert("Please provide a UVA email.", "danger");
            $this->redirectPage("home");
            return;
        }

        $password = $_POST["password"];

        // Check if user exists
        $users = $this->db->query("SELECT * FROM hoos_there_users WHERE email = $1;", $email);
        if (empty($users)) {
            // Validate password
            if (strlen($password) < 8 || !preg_match("/^\w*[!@#$%^&*()\-=_+?]+\w*$/", $password)) {
                $this->createAlert("Please provide a secure password.", "danger");
                $this->redirectPage("home");
                return;
            }
            
            // Create new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $this->db->query(
                "INSERT INTO hoos_there_users (email, password) VALUES ($1, $2)",
                $email, $hashed_password
            );

            // Fetch new user
            $users = $this->db->query("SELECT * FROM hoos_there_users WHERE email = $1;", $email);
            $user = $users[0];
            $this->createAlert("Created new user $email.", "success");
        } else {
            // Verify password
            $user = $users[0];
            if (!password_verify($password, $user["password"])) {
                $this->createAlert("Incorrect password for user $email.", "danger");
                header("Location: ?command=welcome");
                return;
            }

            $this->createAlert("Logged in as $email.", "success");
        }

        $_SESSION["user_id"] = $user["id"];
        
        // TODO redirect to own user profile
        $this->redirectPage("home");
    }

    // Create alert that persists to next load
    private function createAlert($message, $type) {
        $_SESSION["alert"] = ["message" => $message, "type" => $type];
    }

    // Print alert in template
    public function showAlert() {
        if (!isset($_SESSION["alert"])) return;

        $alert = $_SESSION["alert"];
        if (!empty($alert)) {
            echo "<p class=\"alert alert-" . $alert["type"] . "\">";
            echo $alert["message"];
            echo "</p>";
            $_SESSION["alert"] = [];
        }
    }

    private function showTemplate($template) {
        include($this->include_path . $template);
    }

    private function redirectPage($command) {
        header("Location: ?command=$command");
    }

}