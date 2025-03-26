<?php

class AnagramsController {
    private $input;
    private $game = null; 
    private $errorMessage = "";

    public function __construct($input) {
        $this->input = $input;
        if (isset($_SESSION['game'])) {
            $this->game = $_SESSION['game'];
        }
    }

    public function run() {
        $command = isset($this->input["command"]) ? $this->input["command"] : "welcome";

        switch($command) {
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


    private function showWelcome() {
        include(__DIR__ . "/templates/welcome.php");
    }

    private function startGame() {
        $name  = isset($this->input["name"])  ? trim($this->input["name"])  : "";
        $email = isset($this->input["email"]) ? trim($this->input["email"]) : "";

        if ($name === "" || $email === "") {
            $this->errorMessage = "Please provide both name and email!";
            include(__DIR__ . "/templates/welcome.php");
            return;
        }

        $_SESSION["name"]  = $name;
        $_SESSION["email"] = $email;

        $this->game = new AnagramsGame();
        $_SESSION['game'] = $this->game;

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

        // Submit guess to the model
        $result = $this->game->processGuess($guess);

        // Save updated game state to session
        $_SESSION['game'] = $this->game;

        if ($this->game->isGameOver()) {
            // If the user found the 7-letter word, game is done
            header("Location: ?command=gameover");
            exit();
        }

        // Otherwise, show the updated game page
        $this->errorMessage = $result; 
        include(__DIR__ . "/templates/game.php");
    }


    private function playAgain() {
        // If we do NOT have name/email, redirect to welcome
        if (empty($_SESSION["name"]) || empty($_SESSION["email"])) {
            header("Location: ?command=welcome");
            exit();
        }
        // Create a new AnagramsGame with a fresh target word
        $this->game = new AnagramsGame();
        $_SESSION["game"] = $this->game;

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
        session_destroy();
        header("Location: ?command=welcome");
        exit();
    }

    private function showGameOver() {
        if (!$this->game) {
            header("Location: ?command=welcome");
            exit();
        }
        include(__DIR__ . "/templates/gameover.php");
    }
}
