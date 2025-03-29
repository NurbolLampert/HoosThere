<?php

class HoosThereController {

    public function __construct($input, $include_path) {
        $this->input = $input;
        $this->include_path = $include_path;
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

        // Validate password
        $password = $_POST["password"];
        if (strlen($password) < 8 || !preg_match("/^\w*[!@#$%^&*()\-=_+?]+\w*$/", $password)) {
            $this->createAlert("Please provide a secure password.", "danger");
            $this->redirectPage("home");
            return;
        }

        // TODO validate credentials or create user
        $this->createAlert("Logged in as $email.", "success");
        $this->redirectPage("home");

        // TODO redirect to own user profile
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