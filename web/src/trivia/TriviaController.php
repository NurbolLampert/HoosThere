<?php

class TriviaController {
    private $db; 
    private $input; 
    private $errorMessage;  
    public function __construct($input) {
        session_start();
        $this->input = $input; 
        $this->errorMessage = "";


        $this->db = new Database();  
    }

  /**
   * Run the server
   * 
   * Given the input (usually $_GET), then it will determine
   * which command to execute based on the given "command"
   * parameter.  Default is the welcome page.
   */
    public function run() {
        $command = $this->input["command"] ?? "welcome";

        if (!isset($_SESSION["email"]) && $command !== "login" && $command !== "welcome") {
            $command = "welcome";
        }

        switch ($command) {
            case "login":
                $this->login();
                break;
            case "answer":
                $this->answerQuestion();
                break;
            case "question":
                $this->showQuestion();
                break;
            case "logout":
                $this->logout();
                // fall through to welcome
            case "welcome":
            default:
                $this->showWelcome();
                break;
        }
    }

    private function showWelcome($msg = "") {
        $message = $msg ?: $this->errorMessage;
        include("/opt/src/trivia/templates/welcome.php");
    }


    private function login() {
        // Check required fields
        if (empty($_POST["fullname"]) || empty($_POST["email"]) || empty($_POST["password"])) {
            $this->showWelcome("Please provide name, email, and password.");
            return;
        }

        $name = trim($_POST["fullname"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        // See if the user already exists in the DB
        $results = $this->db->query("SELECT * FROM users WHERE email = $1;", $email);

        if ($results === false) {
            // DB error
            $this->showWelcome("Database error checking user.");
            return;
        }

        if (count($results) === 0) {
            // No user with this email => create new user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $this->db->query(
                "INSERT INTO users (name, email, password, score) 
                 VALUES ($1, $2, $3, 0) RETURNING id;",
                $name, $email, $hash
            );
            if ($insert === false) {
                $this->showWelcome("Database error creating user.");
                return;
            }

            // We have a new user
            $_SESSION["user_id"] = $insert[0]["id"];  // from RETURNING
            $_SESSION["name"] = $name;
            $_SESSION["email"] = $email;
            $_SESSION["score"] = 0;
            header("Location: ?command=question");
            return;
        } else {
            // The user exists => verify password
            $row = $results[0];
            $storedHash = $row["password"];

            if (password_verify($password, $storedHash)) {
                // Correct password => set session
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["name"] = $row["name"];
                $_SESSION["email"] = $row["email"];
                // Pull their current DB score
                $_SESSION["score"] = (int)$row["score"];

                header("Location: ?command=question");
                return;
            } else {
                // Wrong password
                $this->showWelcome("Invalid password for that email.");
                return;
            }
        }
    }

    private function showQuestion($message = "") {
        $user_id = $_SESSION["user_id"] ?? null;
        if (!$user_id) {
            $this->showWelcome("Please log in.");
            return;
        }

        // If there's already a question in session, let's see if they answered it:
        if (isset($_SESSION["qid"])) {
            // They might not have answered => load that question again
            $q = $this->getQuestionById($_SESSION["qid"]);
            if ($q !== false) {
                $score = $_SESSION["score"];
                $name = $_SESSION["name"];
                include("/opt/src/trivia/templates/question.php");
                return;
            }
        }

        // Otherwise, let's fetch a random question from DB
        $q = $this->db->query("SELECT id, question, answer 
                               FROM questions
                               ORDER BY random()
                               LIMIT 1;");
        if ($q === false || !isset($q[0])) {
            $this->showWelcome("No questions found in DB.");
            return;
        }

        // Store qid in session
        $_SESSION["qid"] = $q[0]["id"];

        // Pass data to template
        $score = $_SESSION["score"];
        $name = $_SESSION["name"];
        $question = $q[0];
        include("/opt/src/trivia/templates/question.php");
    }


    private function answerQuestion() {
        if (!isset($_SESSION["qid"]) || empty($_POST["answer"])) {
            // No question in session or no answer provided
            $this->showQuestion("Please submit an answer.");
            return;
        }

        $qid = $_SESSION["qid"];
        $userAnswer = trim($_POST["answer"]);

        // Get actual question from DB
        $question = $this->getQuestionById($qid);
        if ($question === false) {
            // Maybe the question ID was invalid or DB error
            $this->showQuestion("Error fetching question.");
            return;
        }

        $correct = (strtolower($userAnswer) == strtolower($question["answer"]));
        $message = "";

        if ($correct) {
            // Increase user’s score
            $_SESSION["score"] += 10;
            $message = "<div class='alert alert-success'>Correct!</div>";

            // Also update DB score
            $this->db->query(
                "UPDATE users SET score = $1 WHERE id = $2;",
                $_SESSION["score"],
                $_SESSION["user_id"]
            );
        } else {
            $message = "<div class='alert alert-danger'>Incorrect!</div>";
        }

        // Clear qid so they get a new question next time
        unset($_SESSION["qid"]);

        // Show new question with message
        $this->showQuestion($message);
    }


    private function getQuestionById($qid) {
        $res = $this->db->query("SELECT id, question, answer FROM questions WHERE id=$1;", $qid);
        if ($res === false || !isset($res[0])) {
            return false;
        }
        return $res[0];
    }

    private function logout() {
        session_destroy();
        session_start();
        $_SESSION["score"] = 0;
        $this->showWelcome("Logged out!");
    }
}
