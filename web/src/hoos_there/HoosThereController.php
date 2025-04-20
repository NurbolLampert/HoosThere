<?php
require_once("services/UsersService.php");
require_once("services/AcademicsService.php");
require_once("services/SocialProfessionalService.php");
/**
 * Front controller for HoosThere app.
 */
class HoosThereController {

    private $input;
    private $include_path;
    private $db;

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

        if ($this->isLoggedIn()) {
            // Save user id in cookie
            // This is obviously insecure and we should probably provide some email/password hash
            setcookie("user_id", $_SESSION["user_id"], time() + 60 * 60, "/"); // 1 hr
        } else if (isset($_COOKIE["user_id"]) && !empty($_COOKIE["user_id"])) {
            // Fetch user id from cookie
            $_SESSION["user_id"] = $_COOKIE["user_id"];
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
            case "update_profile":
                $this->checkLoggedInOrReturnFail();
                $this->updateProfile();
                break;
            case "user_data":
                $this->checkLoggedInOrExit();
                $this->showUserData();
                break;
            case "delete_user":
                $this->checkLoggedInOrExit();
                $this->deleteUser();
                break;
            case "academics":
                $this->checkLoggedInOrExit();
                $this->showAcademics();
                break;
            case "update_record":
                $this->checkLoggedInOrExit();
                $this->updateAcademicRecord();
                break;
            case "add_record":
                $this->checkLoggedInOrExit();
                $this->addAcademicRecord();
                break;
            case "update_project":
                $this->checkLoggedInOrExit();
                $this->updateProject();
                break;
            case "add_project":
                $this->checkLoggedInOrExit();
                $this->addProject();
                break;
            case "update_goals":
                $this->checkLoggedInOrExit();
                $this->updateGoals();
                break;
            case "social":
                $this->checkLoggedInOrExit();
                $this->showSocial();
                break;
            case "update_social_links":
                $this->checkLoggedInOrReturnFail();
                $this->updateSocialLinks();
                break;
            case "add_experience":
                $this->checkLoggedInOrExit();
                $this->addExperience();
                break;
            case "update_experience":
                $this->checkLoggedInOrExit();
                $this->updateExperience();
                break;
            case "add_education":
                $this->checkLoggedInOrExit();
                $this->addEducation();
                break;
            case "update_education":
                $this->checkLoggedInOrExit();
                $this->updateEducation();
                break;
            case "add_club":
                $this->checkLoggedInOrExit();
                $this->addClub();
                break;
            case "update_club":
                $this->checkLoggedInOrExit();
                $this->updateClub();
                break;
            case "add_volunteer":
                $this->checkLoggedInOrExit();
                $this->addVolunteer();
                break;
            case "update_volunteer":
                $this->checkLoggedInOrExit();
                $this->updateVolunteer();
                break;
            case "get_friends":
                $this->checkLoggedInOrReturnFail();
                $this->getFriends();
                break;
            case "add_friend":
                $this->checkLoggedInOrReturnFail();
                $this->addFriend();
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
        $service = new UsersService($this->db);
        $user = $service->getUserByEmail($email);
        if (is_null($user)) {
            $this->createAlert("Email not found. Please register a new account.", "danger");
            $this->redirectPage("home&register=0");
            return;
        }

        // Verify password correct
        if (!password_verify($_POST["password"], $user["password"])) {
            $this->createAlert("Incorrect password for user $email.", "danger");
            header("Location: ?command=welcome");
            return;
        }
        $this->createAlert("Logged in as $email.", "success");

        // Redirect to user profile
        $_SESSION["user_id"] = $user["id"];
        $this->redirectPage("home");
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
        $service = new UsersService($this->db);
        $user = $service->getUserByEmail($email);
        if (!is_null($user)) {
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
        $user = $service->createUser($name, $year, $email, $password);
        $this->createAlert("Created new user $name with email $email.", "success");

        // Redirect to user profile
        $_SESSION["user_id"] = $user["id"];
        $this->redirectPage("profile&id=" . $_SESSION["user_id"]);
    }

    /**
     * Log out the user.
     */
    private function logOut() {
        session_destroy();
        session_start();
        setcookie("user_id", "", 0, "/"); // Clear user id from cookie
        $this->createAlert("Logged out. See you soon!", "success");
        $this->redirectPage("home");
    }

    /**
     * Delete the user's account.
     */
    private function deleteUser() {
        // Validate hidden form input
        if (!isset($_POST["confirm"]) || $_POST["confirm"] != "1") {
            $this->createAlert("Please confirm account deletion!", "danger");
            $this->redirectPage("home");
            return;
        }

        $email = $this->getUserInfo()["email"];
        $user_id = $_SESSION["user_id"];

        session_destroy();
        session_start();
        setcookie("user_id", "", 0, "/"); // Clear user id from cookie

        $service = new UsersService($this->db);
        $service->deleteUser($user_id);
        $this->createAlert("Account $email deleted. Sorry so see you go!", "success");
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
     * Check user is logged in and redirect to the homepage if not.
     */
    private function checkLoggedInOrExit() {
        if (!$this->isLoggedIn()) {
            $this->createAlert("You must be logged in to continue.", "danger");
            $this->redirectPage("home");
            exit(1);
        }
    }

    /** 
     * Check user is logged in return a JSON failure reponse if not.
     */
    private function checkLoggedInOrReturnFail() {
        if (!$this->isLoggedIn()) {
            $data = ["result" => "failure"];
            $this->showJSONResponse($data);
            exit(1);
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

        $service = new UsersService($this->db);
        return $service->getUserByID($user_id);
    }

    /**
     * Get the filename of the user avatar.
     */
    public function getUserAvatar($user_id) {
        $num = (($user_id - 1) % 10) + 1; // 1-10
        return "profile-avatars/avatar$num.png";
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
        $user_id = $this->input["id"] ?? $_SESSION["user_id"]; // Default to own user

        if ($user_id == $_SESSION["user_id"]) {
            // Show own profile
            // Can't use helper method because $user_id will not be visible
            include($this->include_path . "/templates/profile_self.php");
        } else if (!is_null($this->getUserInfo($user_id))) {
            // Show other profile
            include($this->include_path . "/templates/profile_other.php");
        } else {
            // User does not exist
            $this->createAlert("That user does not exist.", "danger");
            $this->redirectPage("profile");
        }
    }

    /**
     * Update the current user's profile.
     */
    private function updateProfile() {
        // Clean form data
        $major = $_POST["major"] ?? "";
        $hometown = $_POST["hometown"] ?? "";
        $description = $_POST["description"] ?? "";
        $user_id = $_SESSION["user_id"];

        $service = new UsersService($this->db);
        $service->updateUserProfile($user_id, $major, $hometown, $description);

        $data = [
            "user_id" => $user_id,
            "major" => $major,
            "hometown" => $hometown,
            "description" => $description,
            "result" => "success"
        ];
        $this->showJSONResponse($data);
    }

    private function showAcademics() {
        $this->checkLoggedInOrExit();
        $service = new AcademicsService($this->db);
        $academic_data = $service->getRecords($_SESSION["user_id"]);
        include($this->include_path . "/templates/academics.php");
    }

    private function updateAcademicRecord() {
        $this->checkLoggedInOrExit();
        if (!isset($_POST["record_id"])) {
            $this->createAlert("Missing record ID", "danger");
            $this->redirectPage("academics");
            return;
        }
    
        $service = new AcademicsService($this->db);
        $service->updateRecord(
            $_POST["record_id"],
            $_POST["course_code"],
            $_POST["course_name"],
            $_POST["teammate_name"] ?? '',
            $_POST["project_title"] ?? '',
            $_POST["karma"] ?? null
        );
    
        $this->createAlert("Record updated.", "success");
        $this->redirectPage("academics");
    }

    private function addAcademicRecord() {
        $user_id = $_SESSION["user_id"];
        $year = $_POST["year"];
        $term = $_POST["term"];
        $code = $_POST["course_code"];
        $name = $_POST["course_name"];
        $teammate = $_POST["teammate_name"] ?? '';
        $project = $_POST["project_title"] ?? '';
        $karma = $_POST["karma"] ?? null;
    
        $service = new AcademicsService($this->db);
        $service->insertRecord($user_id, $year, $term, $code, $name, $teammate, $project, $karma);
    
        $this->createAlert("New academic record added!", "success");
        $this->redirectPage("academics");
    }


    private function updateProject() {
        $user_id = $_SESSION["user_id"];
        $id = $this->input["id"] ?? null;
        $title = $_POST["project_title"] ?? '';
        $desc = $_POST["project_description"] ?? '';
    
        if (!$id) {
            $this->createAlert("Missing project ID.", "danger");
            $this->redirectPage("academics");
            return;
        }
    
        $service = new AcademicsService($this->db);
        $service->updateProject($user_id, $id, $title, $desc);
    
        $this->createAlert("Project updated!", "success");
        $this->redirectPage("academics");
    }
    
    private function addProject() {
        if (!isset($_POST["project_title"]) || !isset($_POST["description"])) {
            $this->createAlert("Missing project title or description", "danger");
            $this->redirectPage("academics");
            return;
        }
    
        $service = new AcademicsService($this->db);
        $service->addProject($_SESSION["user_id"], $_POST["project_title"], $_POST["description"]);
        $this->createAlert("Project added!", "success");
        $this->redirectPage("academics");
    }
    
    private function updateGoals() {
        if (!isset($_POST["goal_description"])) {
            $this->createAlert("Missing goal", "danger");
            $this->redirectPage("academics");
            return;
        }
    
        $service = new AcademicsService($this->db);
        $service->updateGoal($_SESSION["user_id"], $_POST["goal_description"]);
        $this->createAlert("Goals updated!", "success");
        $this->redirectPage("academics");
    }
    
    private function showSocial() {
        $service = new SocialProfessionalService($this->db);
        $data = $service->getSocialProfessionalData($_SESSION["user_id"]);
        include($this->include_path . "/templates/social_professional_life.php");
    }
    
    private function updateSocialLinks() {
        $service = new SocialProfessionalService($this->db);
        $user_id = $_SESSION["user_id"];
        $instagram = $_POST["instagram"] ?? '';
        $linkedin = $_POST["linkedin"] ?? '';
        $facebook = $_POST["facebook"] ?? '';

        $service->updateSocialLinks(
            $user_id, $instagram, $linkedin, $facebook
        );

        $data = [
            "user_id" => $user_id,
            "instagram" => $instagram,
            "linkedin" => $linkedin,
            "facebook" => $facebook,
            "result" => "success"
        ];
        $this->showJSONResponse($data);
    }
    
    private function addExperience() {
        $service = new SocialProfessionalService($this->db);
        $service->addExperience(
            $_SESSION["user_id"],
            $_POST["role"] ?? '',
            $_POST["description"] ?? ''
        );
        $this->createAlert("Experience added.", "success");
        $this->redirectPage("social");
    }
    
    private function updateExperience() {
        $service = new SocialProfessionalService($this->db);
        $service->updateExperience(
            $_POST["id"],
            $_POST["role"] ?? '',
            $_POST["description"] ?? ''
        );
        $this->createAlert("Experience updated.", "success");
        $this->redirectPage("social");
    }

    private function addEducation() {
        $service = new SocialProfessionalService($this->db);
        $service->addEducation(
            $_SESSION["user_id"],
            $_POST["degree"] ?? '',
            $_POST["institution"] ?? '',
            $_POST["expected_graduation"] ?? ''
        );
        $this->createAlert("Education added.", "success");
        $this->redirectPage("social");
    }
    
    private function updateEducation() {
        $service = new SocialProfessionalService($this->db);
        $service->updateEducation(
            $_POST["id"],
            $_POST["degree"] ?? '',
            $_POST["institution"] ?? '',
            $_POST["expected_graduation"] ?? ''
        );
        $this->createAlert("Education updated.", "success");
        $this->redirectPage("social");
    }
    
    private function addClub() {
        $service = new SocialProfessionalService($this->db);
        $service->addClub(
            $_SESSION["user_id"],
            $_POST["name"] ?? '',
            $_POST["role"] ?? '',
            $_POST["year"] ?? ''
        );
        $this->createAlert("Club/Organization added.", "success");
        $this->redirectPage("social");
    }
    
    private function updateClub() {
        $service = new SocialProfessionalService($this->db);
        $service->updateClub(
            $_POST["id"],
            $_POST["name"] ?? '',
            $_POST["role"] ?? '',
            $_POST["year"] ?? ''
        );
        $this->createAlert("Club/Organization updated.", "success");
        $this->redirectPage("social");
    }
    
    private function addVolunteer() {
        $service = new SocialProfessionalService($this->db);
        $service->addVolunteer(
            $_SESSION["user_id"],
            $_POST["organization"] ?? '',
            $_POST["description"] ?? ''
        );
        $this->createAlert("Volunteer experience added.", "success");
        $this->redirectPage("social");
    }
    
    private function updateVolunteer() {
        $service = new SocialProfessionalService($this->db);
        $service->updateVolunteer(
            $_POST["id"],
            $_POST["organization"] ?? '',
            $_POST["description"] ?? ''
        );
        $this->createAlert("Volunteer experience updated.", "success");
        $this->redirectPage("social");
    }

    private function getFriends() {
        $user_id = $_SESSION["user_id"];
        $data = [
            "user_id" => $user_id,
            "result" => "success",
            "friends" => []
        ];

        // Get friend IDs, names and avatars
        $service = new UsersService($this->db);
        $friends = $service->getFriendsList($user_id);
        foreach ($friends as $friend) {
            $avatar = $this->getUserAvatar($friend["id"]);
            $friend["avatar"] = $avatar;
            $data["friends"][] = $friend;
        }
        
        $this->showJSONResponse($data);
    }

    private function addFriend() {
        $user_id = $_SESSION["user_id"];

        // Look up friend id by name
        $name = trim($_POST["name"]) ?? "";
        $service = new UsersService($this->db);
        $friend = $service->getUserByName($name);
        
        if (is_null($friend)) {
            $friend_id = null;
        } else {
            $friend_id = $friend["id"];
        }

        if (is_null($friend_id)) { 
            // Check user exists
            $data = [
                "result" => "failure",
                "message" => "That user does not exist."
            ];
            $this->showJSONResponse($data);
            return;
        } else if ($user_id == $friend_id) {
            // Check user exists
            $data = [
                "result" => "failure",
                "message" => "You cannot friend yourself!"
            ];
            $this->showJSONResponse($data);
            return;
        } else if (!empty($service->areUsersFriends($user_id, $friend_id))) {
            // Make sure not already friends
            $data = [
                "result" => "failure",
                "message" => "You are already friends with this user."
            ];
            $this->showJSONResponse($data);
            return;
        }
        
        $friend_json = [
            "id" => $friend_id,
            "name" => $friend["name"],
            "avatar" => $this->getUserAvatar($friend["id"])
        ];

        // Add friends
        $service = new UsersService($this->db);
        $service->addFriends($user_id, $friend_id);
        
        $data = [
            "user_id" => $user_id,
            "friend" => $friend_json,
            "result" => "success"
        ];
        $this->showJSONResponse($data);
    }
    

    /**
     * Return the current user's data in JSON form.
     */
    private function showUserData() {
        $data = $this->getUserInfo();
        $this->showJSONResponse($data);
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

    private function showJSONResponse($obj) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($obj);
    }

}