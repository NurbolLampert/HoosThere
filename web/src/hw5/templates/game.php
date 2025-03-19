<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Anagrams - Game</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-md-8 offset-md-2">
      <div class="card shadow">
        <div class="card-header bg-secondary text-white">
          <h2>Anagrams Game</h2>
        </div>
        <div class="card-body">

          <?php if (!empty($this->errorMessage)) : ?>
            <div class="alert alert-info">
              <?= htmlspecialchars($this->errorMessage) ?>
            </div>
          <?php endif; ?>

          <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION["name"] ?? "Unknown") ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION["email"] ?? "Unknown") ?></p>
          <p><strong>Score:</strong> <?= $this->game->getScore() ?></p>

          <p><strong>Shuffled Letters:</strong> 
            <span class="fs-4"><?= htmlspecialchars($this->game->getShuffledLetters()) ?></span>
          </p>

          <div class="mb-3">
            <small class="text-muted">
              Only the specific 7-letter word (unshuffled) that started the game 
              will end it if guessed. The dictionary does not list other 7-letter words.
            </small>
          </div>

          <!-- Guess form -->
          <form action="?command=submit" method="post" class="mb-3">
            <div class="mb-3">
              <label for="guess" class="form-label">Enter your guess:</label>
              <input type="text" class="form-control" name="guess" id="guess" required />
            </div>
            <button type="submit" class="btn btn-primary">Submit Guess</button>
          </form>

          <p><strong>Valid Words Guessed:</strong></p>
          <?php if (count($this->game->getGuessedWords()) > 0): ?>
            <ul>
              <?php foreach ($this->game->getGuessedWords() as $word): ?>
                <li><?= htmlspecialchars($word) ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>No valid words guessed yet.</p>
          <?php endif; ?>

        </div>
        <div class="card-footer d-flex justify-content-between">
          <a href="?command=shuffle" class="btn btn-outline-secondary">Shuffle Letters</a>
          <a href="?command=quit" class="btn btn-danger">Quit Game</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
