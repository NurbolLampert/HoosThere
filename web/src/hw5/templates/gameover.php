<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Anagrams - Game Over</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-dark text-white">
      <h2>Game Over!</h2>
    </div>
    <div class="card-body">
      <p class="lead">
        Great job, <?= htmlspecialchars($_SESSION["name"] ?? "") ?>!
      </p>
      <p>Your final score is <strong><?= $this->game->getScore() ?></strong>.</p>
      <p>You guessed the following valid words:</p>
      <ul>
        <?php foreach ($this->game->getGuessedWords() as $w): ?>
          <li><?= htmlspecialchars($w) ?></li>
        <?php endforeach; ?>
      </ul>
      <p>Invalid guesses: <strong><?= $this->game->getInvalidGuessCount() ?></strong></p>
    </div>
    <div class="card-footer d-flex justify-content-between">
      <!-- "Play Again" reuses name/email, starts a brand new game -->
      <a href="?command=playAgain" class="btn btn-primary">Play Again</a>
      <!-- "Exit" ends the session and sends user to welcome -->
      <a href="?command=exit" class="btn btn-danger">Exit</a>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
