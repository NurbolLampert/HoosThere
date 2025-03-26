<?php

class AnagramsController {
    private $input;
    private $game = null; 
    private $errorMessage = "";
    private $db; 

    public function __construct($input) {
        if (!isset($_SESSION)) { 
            session_start(); 
        }
        $this->input = $input;
        if (isset($_SESSION['game'])) {
            $this->game = $_SESSION['game'];
        }

        $this->db = new Database();
    }

    public function run() {
        $command = isset($this->input["command"]) ? $this->input["command"] : "welcome";

        switch($command) {
            case "login":
                $this->handleLogin();
                break;
            case "playAgain":
                $this->playAgain();
                break;
            case "start":
                $this->startGame();
                break;
            case "submit":
                $this->submitGuess();
                break;
            case "shuffle":
                $this->shuffleLetters();
                break;
            case "quit":
                $this->showGameOver();
                break;
            case "exit":
                $this->quitGame();
                break;
            case "gameover":
                $this->showGameOver();
                break;
            case "welcome":
            default:
                $this->showWelcome();
                break;
        }
    }

    private function handleLogin() {
        $name     = trim($this->input["name"] ?? "");
        $email    = trim($this->input["email"] ?? "");
        $password = trim($this->input["password"] ?? "");

        if ($name === "" || $email === "" || $password === "") {
            $this->errorMessage = "Please provide name, email, and password!";
            include(__DIR__ . "/templates/welcome.php");
            return;
        }

        $sql  = "SELECT id, name, password_hash FROM hw6_users WHERE email = $1;";
        $rows = $this->db->query($sql, $email);

        // If query error
        if ($rows === false) {
            $this->errorMessage = "Database query error.";
            include(__DIR__ . "/templates/welcome.php");
            return;
        }

        // If NO rows are returned => create new user
        if (!$rows || count($rows) === 0) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insertSql = "INSERT INTO hw6_users(name, email, password_hash) 
                          VALUES($1, $2, $3) RETURNING id;";
            $newRow = $this->db->query($insertSql, $name, $email, $hash);

            if (!$newRow || count($newRow) === 0) {
                $this->errorMessage = "Error creating new user.";
                include(__DIR__ . "/templates/welcome.php");
                return;
            }

            $userId = $newRow[0]["id"];
            $_SESSION["user_id"] = $userId;
            $_SESSION["name"]    = $name;
            $_SESSION["email"]   = $email;

            $this->errorMessage = "New User Created: {$email}!";
            $this->startGame();
        } else {
            // user found => check password
            $row = $rows[0];
            $hashFromDb = $row["password_hash"] ?? "";
            if (password_verify($password, $hashFromDb)) {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["name"]    = $row["name"];
                $_SESSION["email"]   = $email;
                $this->errorMessage = "Welcome Back {$email}!";
                $this->startGame();
            } else {
                $this->errorMessage = "Incorrect password. {$email}";
                include(__DIR__ . "/templates/welcome.php");
            }
        }
    }

    private function showWelcome() {
        include(__DIR__ . "/templates/welcome.php");
    }


    private function startGame() {
        $name  = $_SESSION["name"]  ?? "";
        $email = $_SESSION["email"] ?? "";

        if ($name === "" || $email === "") {
            $this->errorMessage = "Please provide both name and email!";
            include(__DIR__ . "/templates/welcome.php");
            return;
        }

        // create a new game object
        $this->game = new AnagramsGame();
        $_SESSION['game'] = $this->game;

        // Insert into hw6_games to track stats
        $userId = $_SESSION["user_id"];
        $sql = "INSERT INTO hw6_games(user_id, score, won) 
                VALUES($1, 0, FALSE) RETURNING id;";
        $res = $this->db->query($sql, $userId);

        if ($res === false || count($res) === 0) {
            $this->errorMessage = "Error creating game record in database.";
            include(__DIR__ . "/templates/welcome.php");
            return;
        }

        // store that game ID in session so we can update it later
        $_SESSION["game_id"] = $res[0]["id"];

        include(__DIR__ . "/templates/game.php");
    }

    private function submitGuess() {
        if (!$this->game) {
            header("Location: ?command=welcome");
            exit();
        }
        $guess = isset($this->input["guess"]) ? trim($this->input["guess"]) : "";
        if ($guess === "") {
            $this->errorMessage = "Please enter a word to guess.";
            include(__DIR__ . "/templates/game.php");
            return;
        }

        $result = $this->game->processGuess($guess);
        $_SESSION['game'] = $this->game; 

        if ($this->game->isGameOver()) {
            // update the hw6_games row: set won=TRUE, score=the final
            $this->finalizeGame(true);
            header("Location: ?command=gameover");
            exit();
        }

        $this->updateScoreInDB($this->game->getScore());

        $this->errorMessage = $result; 
        include(__DIR__ . "/templates/game.php");
    }


    private function playAgain() {
        if (empty($_SESSION["name"]) || empty($_SESSION["email"])) {
            header("Location: ?command=welcome");
            exit();
        }

        $this->game = new AnagramsGame();
        $_SESSION["game"] = $this->game;

        // Insert new row in hw6_games
        $userId = $_SESSION["user_id"];
        $sql = "INSERT INTO hw6_games(user_id, score, won) 
                VALUES($1, 0, FALSE) RETURNING id;";
        $res = $this->db->query($sql, $userId);
        if ($res !== false && count($res) > 0) {
            $_SESSION["game_id"] = $res[0]["id"];
        }

        include(__DIR__ . "/templates/game.php");
    }

    private function shuffleLetters() {
        if (!$this->game) {
            header("Location: ?command=welcome");
            exit();
        }
        $this->game->shuffleLetters();
        $_SESSION['game'] = $this->game;
        include(__DIR__ . "/templates/game.php");
    }

    private function quitGame() {
        // If there's an ongoing game that isn't finished, finalize as lost
        if (!empty($_SESSION["game_id"]) && !$this->game->isGameOver()) {
            $this->finalizeGame(false);
        }
        session_destroy();
        header("Location: ?command=welcome");
        exit();
    }

    private function showGameOver() {
        if (!$this->game) {
            header("Location: ?command=welcome");
            exit();
        }
        // If user never guessed the word, finalize as lost
        if (!empty($_SESSION["game_id"]) && !$this->game->isGameOver()) {
            $this->finalizeGame(false);
        }
        include(__DIR__ . "/templates/gameover.php");
    }

    private function finalizeGame($won) {
        if (empty($_SESSION["game_id"])) {
            return;
        }
        $sql = "UPDATE hw6_games
                SET won=$1, 
                    score=$2,
                    date_finished = NOW()
                WHERE id=$3;";
        $this->db->query($sql, $won ? 'TRUE' : 'FALSE', $this->game->getScore(), $_SESSION["game_id"]);
    }


    private function updateScoreInDB($score) {
        if (empty($_SESSION["game_id"])) {
            return;
        }
        $sql = "UPDATE hw6_games
                SET score = $1
                WHERE id = $2;";
        $this->db->query($sql, $score, $_SESSION["game_id"]);
    }

    public function getUserStats($userId) {
        // total games played & total won
        $sql1 = "
          SELECT COUNT(*) AS total_played,
                 SUM(CASE WHEN won THEN 1 ELSE 0 END) AS total_won
          FROM hw6_games
          WHERE user_id = $1
        ";
        $res1 = $this->db->query($sql1, $userId);
        if (!$res1 || count($res1) === 0) {
            return [
                "played" => 0,
                "won" => 0,
                "wonPct" => 0,
                "highest" => 0,
                "avg" => 0
            ];
        }
        $played = (int)$res1[0]["total_played"];
        $won    = (int)$res1[0]["total_won"];
        $wonPct = ($played > 0) ? ($won / $played) * 100 : 0;

        // highest score
        $sql2 = "
          SELECT COALESCE(MAX(score),0) AS highest
          FROM hw6_games
          WHERE user_id=$1
        ";
        $res2 = $this->db->query($sql2, $userId);
        $highest = (int)$res2[0]["highest"];

        // average score
        $sql3 = "
          SELECT COALESCE(AVG(score),0) AS avg_score
          FROM hw6_games
          WHERE user_id=$1
        ";
        $res3 = $this->db->query($sql3, $userId);
        $avg   = (float)$res3[0]["avg_score"];

        return [
            "played"  => $played,
            "won"     => $won,
            "wonPct"  => $wonPct,
            "highest" => $highest,
            "avg"     => $avg
        ];
    }
}
