<?php

class AnagramsGame {
    private $targetWord;        
    private $shuffledLetters;   
    private $score;
    private $guessedWords;      
    private $invalidGuessCount;

    private $shortWords = [];  

    public function __construct() {
        $this->loadWordLists();
        $this->targetWord = $this->pickRandomWord();
        $this->shuffledLetters = str_shuffle($this->targetWord);

        $this->score = 0;
        $this->guessedWords = [];
        $this->invalidGuessCount = 0;
    }

    private function loadWordLists() {
        // $words7Path  = __DIR__ . "/data/words7.txt";
        // $jsonPath    = __DIR__ . "/data/word_bank.json";

        $words7Path = "/var/www/html/homework/words7.txt";
        $jsonPath   = "/var/www/html/homework/word_bank.json";

        if (file_exists($words7Path)) {
            // read each line as a separate word
            $words7 = array_map('trim', file($words7Path));
            $this->words7 = $words7; 
        }

        if (file_exists($jsonPath)) {
            $jsonData = file_get_contents($jsonPath);
            $this->shortWords = json_decode($jsonData, true);
        }
    }

    private function pickRandomWord() {
        if (!empty($this->words7)) {
            return $this->words7[array_rand($this->words7)];
        }
        // fallback
        return "example";
    }

    public function shuffleLetters() {
        $this->shuffledLetters = str_shuffle($this->targetWord);
    }

    public function processGuess($guess) {
        $guess = strtolower($guess);

        // 1) check letters
        if (!$this->isSubsetOfTarget($guess)) {
            $this->invalidGuessCount++;
            return "You used letters that aren't in the target!";
        }

        // 2) check dictionary
        if (!$this->isWordValid($guess)) {
            $this->invalidGuessCount++;
            return "Not a valid word!";
        }

        // 3) if this word was guessed before
        if (in_array($guess, $this->guessedWords)) {
            return "Already guessed '$guess'!";
        }

        // 4) valid new guess => record it
        $this->guessedWords[] = $guess;
        $this->score += $this->calculatePoints(strlen($guess));

        // 5) if guess is the 7-letter target, we are done
        if ($guess === $this->targetWord) {
            return "You found the full 7-letter word! Game Over!";
        }

        return "Nice! You guessed a valid word.";
    }

    private function isSubsetOfTarget($guess) {
        $targetFreq = $this->getLetterCount($this->targetWord);
        $guessFreq  = $this->getLetterCount($guess);

        foreach ($guessFreq as $letter => $count) {
            if (!isset($targetFreq[$letter]) || $count > $targetFreq[$letter]) {
                return false; 
            }
        }
        return true;
    }

    private function getLetterCount($word) {
        $freq = [];
        $chars = str_split($word);
        foreach ($chars as $c) {
            if (!isset($freq[$c])) {
                $freq[$c] = 0;
            }
            $freq[$c]++;
        }
        return $freq;
    }

    private function isWordValid($guess) {
        $len = strlen($guess);
            $lenKey = (string)$len;
    
        if (!isset($this->shortWords[$lenKey])) {
            return false; 
        }
    
        return in_array($guess, $this->shortWords[$lenKey]);
    }
    

    private function calculatePoints($length) {
        switch($length) {
            case 1: return 1;
            case 2: return 2;
            case 3: return 4;
            case 4: return 8;
            case 5: return 15;
            case 6: return 30;
            default: return 0;
        }
    }

    public function isGameOver() {
        return in_array($this->targetWord, $this->guessedWords);
    }

    public function getShuffledLetters()    { return $this->shuffledLetters; }
    public function getScore()             { return $this->score; }
    public function getGuessedWords()      { return $this->guessedWords; }
    public function getInvalidGuessCount() { return $this->invalidGuessCount; }
    public function getTargetWord()        { return $this->targetWord; }
}
