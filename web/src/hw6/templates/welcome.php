<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Anagrams - Welcome</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-md-6 offset-md-3">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h2>Welcome to Anagrams!</h2>
        </div>
        <div class="card-body">
          <?php if (!empty($this->errorMessage)) : ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($this->errorMessage) ?>
            </div>
          <?php endif; ?>

          <form action="?command=login" method="post">
            <div class="mb-3">
              <label for="name" class="form-label">Name:</label>
              <input type="text" class="form-control" id="name" name="name" required />
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email:</label>
              <input type="email" class="form-control" id="email" name="email" required />
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password:</label>
              <input type="password" class="form-control" id="password" name="password" required />
            </div>
            <button type="submit" class="btn btn-success">Login / Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
