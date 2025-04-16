function queryWord() {
    return new Promise(resolve => {
      const ajax = new XMLHttpRequest();
      ajax.open("GET", "https://cs4640.cs.virginia.edu/homework/anagrams.php", true);
      ajax.responseType = "json";
      ajax.send(null);
      ajax.addEventListener("load", function() {
        if (this.status === 200) resolve(this.response);
        else console.error("HTTP error fetching new word:", this.status);
      });
      ajax.addEventListener("error", () =>
        console.error("Connection failed fetching new word.")
      );
    });
  }
  

  async function getRandomWord(callback) {
    const newWord = await queryWord();
    callback(newWord);
  }
  
window.addEventListener("DOMContentLoaded", () => {
    // Elements
    const lettersEl = document.getElementById("letters");
    const guessForm = document.getElementById("guess-form");
    const guessInput = document.getElementById("guess-input");
    const guessBtn = document.getElementById("guess-btn");
    const shuffleBtn = document.getElementById("shuffle-btn");
    const newGameBtn = document.getElementById("newgame-btn");
    const feedbackEl = document.getElementById("feedback");
    const guessedUl = document.getElementById("guessed-list");
    const scoreEl = document.getElementById("score");
    const clearBtn = document.getElementById("clear-btn");
  
    // State
    let gameState = { target: "", shuffled: [], guesses: [], score: 0, invalidCount: 0 };
    let stats     = { played: 0, high: 0, low: Infinity, totalCorrectWords: 0, totalIncorrectWords: 0 };
  
    // Load or start
    loadState();
    if (gameState.target) {
        renderGame();
    } else {
        startNewGame();
    }
    renderStats();

    // Events
    newGameBtn.onclick = () => {
        finishCurrentGame();
        startNewGame();
    };
  
    shuffleBtn.onclick = () => {
        gameState.shuffled = shuffleArray(gameState.shuffled);
        renderLetters();
        saveState();
    };
  
    guessForm.onsubmit = async e => {
        e.preventDefault();
        const guess = guessInput.value.trim().toLowerCase();
        guessInput.value = "";
        feedbackEl.textContent = "";

        if (guess === gameState.target) {
            feedbackEl.textContent = `You got it! Final score: "${gameState.score}". Please press New Game for another word`;
            finishCurrentGame(true);
            guessInput.disabled = shuffleBtn.disabled =  guessBtn.disabled = true;
            return;
        }
  
        if (!isSubset(guess, gameState.target)) {
            feedbackEl.textContent = "Invalid letters!";
            gameState.invalidCount++;
            saveState();
            return;
        }
  
        // Build a Request for dictionary check
        const headers = new Headers({ "Content-Type": "application/json" });
        const dictReq = new Request(
            "https://cs4640.cs.virginia.edu/homework/checkword.php",
            {
            method: "POST",
            headers,
            body: JSON.stringify({ word: guess }),
            }
        );
        const dictRes = await fetch(dictReq);
        const dictJ  = await dictRes.json();
        if (!dictJ.valid) {
            feedbackEl.textContent = `"${guess}" not in dictionary.`;
            gameState.invalidCount++;
            saveState();
            return;
        }

        if (gameState.guesses.includes(guess)) {
            feedbackEl.textContent = `"${guess}" already guessed.`;
            return;
        }

        const pts = pointsForLength(guess.length);
        gameState.guesses.push(guess);
        gameState.score += pts;
        feedbackEl.textContent = `+${pts} points!`;
        renderGuessed();
        renderScore();
        saveState();
    };

    clearBtn.onclick = () => {
    localStorage.clear();
    stats = { played:0, high:0, low:Infinity, totalCorrectWords:0, totalIncorrectWords:0 };
    startNewGame();
    renderStats();
    };

    // Core
    function startNewGame() {
    getRandomWord(obj => {
        gameState = {
        target: obj.word.toLowerCase(),
        shuffled: shuffleArray(obj.word.toLowerCase().split("")),
        guesses: [],
        score: 0,
        invalidCount: 0
        };
        guessInput.disabled = shuffleBtn.disabled = false;
        feedbackEl.textContent = "";
        renderGame();
        saveState();
    });
    }

    function finishCurrentGame() {
        if (!gameState.target) return;
        stats.played++;
        stats.high = Math.max(stats.high, gameState.score);
        stats.low  = Math.min(stats.low,  gameState.score);
        stats.totalCorrectWords   += gameState.guesses.length;
        stats.totalIncorrectWords += gameState.invalidCount;
        renderStats();
        saveState();
    }

    function renderGame() {
        renderLetters();
        renderGuessed();
        renderScore();
    }

    function renderLetters() {
        lettersEl.textContent = gameState.shuffled.join(" ");
    }

    function renderGuessed() {
        guessedUl.innerHTML = "";
        const byLen = {};
        gameState.guesses.forEach(w => {
            const len = w.length;
            if(!byLen[len]) {
                byLen[len] = [];
            }
            byLen[len].push(w);
        });
        Object.keys(byLen).sort((a,b)=>a-b).forEach(len => {
            const li = document.createElement("li");
            li.textContent = `${len}-letter: ${byLen[len].join(", ")}`;
            guessedUl.appendChild(li);
        });
    }

    function renderScore() {
        scoreEl.textContent = gameState.score;
    }

    function renderStats() {
        document.getElementById("stat-played").textContent  = stats.played;
        document.getElementById("stat-high")  .textContent  = stats.high;
        document.getElementById("stat-low")   .textContent  = stats.low===Infinity?0:stats.low;
        document.getElementById("stat-avg-corr").textContent =
            stats.played ? (stats.totalCorrectWords / stats.played).toFixed(2) : "0";
        document.getElementById("stat-avg-inc") .textContent =
            stats.played ? (stats.totalIncorrectWords / stats.played).toFixed(2) : "0";
    }

    // --- Helpers ---
    function shuffleArray(arr) {
        for (let i = arr.length-1; i > 0; i--) {
            const j = Math.floor(Math.random()*(i+1));
            [arr[i],arr[j]] = [arr[j],arr[i]];
        }
        return arr;
    }

    function isSubset(guess, target) {
        const freq = s => s.split("").reduce((o,c)=>(o[c]=(o[c]||0)+1,o),{});
        const gf = freq(guess), tf = freq(target);
        return Object.keys(gf).every(c => gf[c] <= (tf[c]||0));
    }

    function pointsForLength(n) {
        return [0,1,2,4,8,15,30][n] || 0;
    }

    function saveState() {
        localStorage.setItem("anagramsGame", JSON.stringify(gameState));
        localStorage.setItem("anagramsStats", JSON.stringify(stats));
    }
    function loadState() {
        const g = JSON.parse(localStorage.getItem("anagramsGame") || "null");
        const s = JSON.parse(localStorage.getItem("anagramsStats") || "null");
        if (g) gameState = g;
        if (s) stats     = s;
    }
});
