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
                $this->logIn();
                break;
            case "register":
                $this->register();
                break;
            case "logout":
                $this->logOut();
                break;
            case "profile":
                $this->checkLoggedInOrExit();
                $this->showProfile();
                break;
            case "home":
            default:
                $this->showHome();
                break;
        }
    }

    // User Account Methods

    /**
     * Log in the user.
     */
    private function logIn() {
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

        // Check if user exists
        $users = $this->db->query("SELECT * FROM hoos_there_users WHERE email = $1;", $email);
        if (empty($users)) {
            $this->createAlert("Email not found. Please register a new account.", "danger");
            $this->redirectPage("home&register=0");
            return;
        }

        // Verify password correct
        $user = $users[0];
        if (!password_verify($_POST["password"], $user["password"])) {
            $this->createAlert("Incorrect password for user $email.", "danger");
            header("Location: ?command=welcome");
            return;
        }
        $this->createAlert("Logged in as $email.", "success");

        // Redirect to user profile
        $_SESSION["user_id"] = $user["id"];
        $this->redirectPage("profile&id=" . $user["id"]);
    }

    /**
     * Create a user account.
     */
    private function register() {
        // Validate form is not empty
        if (
            !isset($_POST["name"]) || empty($_POST["name"]) ||
            !isset($_POST["year"]) || empty($_POST["year"]) ||
            !isset($_POST["email"]) || empty($_POST["email"]) ||
            !isset($_POST["password"]) || empty($_POST["password"])
        ) {
            $this->createAlert("Please fill out all fields.", "danger");
            $this->redirectPage("home&register=1");
            return;
        }

        // Validate year is positive int
        $year = $_POST["year"];
        if (!is_numeric($year) || intval($year) <= 0) {
            $this->createAlert("Graduating year must be a positive integer.", "danger");
            $this->redirectPage("home&register=1");
            return;
        }

        // Validate email
        $email = $_POST["email"];
        if (!preg_match("/^\w+@virginia.edu$/", $email)) {
            $this->createAlert("Please provide a UVA email.", "danger");
            $this->redirectPage("home&register=1");
            return;
        }

        // Check if user exists
        $users = $this->db->query("SELECT * FROM hoos_there_users WHERE email = $1;", $email);
        if (!empty($users)) {
            $this->createAlert("Account already exists. Please login.", "danger");
            $this->redirectPage("home&register=1");
            return;
        }

        // Validate password requirements
        $password = $_POST["password"];
        if (strlen($password) < 8 || !preg_match("/^\w*[!@#$%^&*()\-=_+?]+\w*$/", $password)) {
            $this->createAlert("Please provide a secure password.", "danger");
            $this->redirectPage("home&register=1");
            return;
        }
        
        // Create new user
        $name = $_POST["name"];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $this->db->query(
            "INSERT INTO hoos_there_users (name, year, email, password)
            VALUES ($1, $2, $3, $4)",
            $name, $year, $email, $hashed_password
        );

        // Fetch new user
        $users = $this->db->query("SELECT * FROM hoos_there_users WHERE email = $1;", $email);
        $user = $users[0];
        $this->createAlert("Created new user $name with email $email.", "success");

        // Redirect to user profile
        $_SESSION["user_id"] = $user["id"];
        $this->redirectPage("profile&id=" . $user["id"]);
    }

    /**
     * Log out the user.
     */
    private function logOut() {
        session_destroy();
        session_start();
        $this->createAlert("Logged out. See you soon!", "success");
        $this->redirectPage("home");
    }

    // Account Helper Methods

    /**
     * If the user is logged in.
     */
    private function isLoggedIn() {
        return isset($_SESSION["user_id"]) && is_numeric($_SESSION["user_id"]);
    }

    /** 
     * Check user is logged in and exit if not.
     */
    private function checkLoggedInOrExit() {
        if (!$this->isLoggedIn()) {
            $this->createAlert("You must be logged in to continue.", "danger");
            $this->redirectPage("home");
        }
    }

    /**
     * Get all attributes of the user with the specified ID.
     * Defaults to current user.
     */
    public function getUserInfo($user_id = null) {
        if (is_null($user_id)) {
            $user_id = $_SESSION["user_id"];
        }

        $users = $this->db->query("SELECT * FROM hoos_there_users WHERE id = $1;", $user_id);
        if (empty($users)) {
            return array();
        } else {
            return $users[0];
        }
    }

    // Show View Methods

    /**
     * Show the home screen.
     */
    private function showHome() {
        if ($this->isLoggedIn()) {
            // Logout screen
            $this->showTemplate("home_logged_in.php");
            return;
        }

        if (isset($this->input["register"])) {
            $register = $this->input["register"];
        } else {
            $register = false;
        }

        if ($register) {
            // Create account screen
            $this->showTemplate("home_register.php");
        } else {
            // Log in screen
            $this->showTemplate("home.php");
        }
    }

    /**
     * Show the profile of the specified user.
     */
    private function showProfile() {
        // Get id of user to view
        if (isset($this->input["id"])) {
            $user_id = $this->input["id"];
        } else {
            $user_id = $_SESSION["user_id"]; // Default to own user
        }

        if ($user_id == $_SESSION["user_id"]) {
            // Show own profile
            $this->showTemplate("profile_self.php");
        } else {
            // Show other profile
            $this->showTemplate("profile_other.php");
        }        
    }

    // View Helper Methods

    /**
     * Create alert that persists to next load.
     */
    private function createAlert($message, $type) {
        $_SESSION["alert"] = ["message" => $message, "type" => $type];
    }

    /**
     * Print alert in template.
     */
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
        include($this->include_path . "/templates/" . $template);
    }

    private function redirectPage($command) {
        header("Location: ?command=$command");
    }

}