<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="authors" content="Nurbol Lampert, Eric Weng">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hoo’s There - Home</title>
  <link 
    rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
  >
  <link rel="stylesheet" href="styles/main.css">
</head>
<body>
  <?php
    $this->showTemplate("navbar.php");
  ?>

  <main class="container my-4">
    <h1>Welcome to Hoo's There!</h1>
    <p>Log in or register to start connecting with your classmates.</p>

    <div class="row">
      <?php $this->showAlert(); ?>
    </div>

    <h2>User Login</h2>

    <!-- Login Form -->
    <form action="?command=login" method="post" class="mb-4">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input 
          type="email" 
          class="form-control" 
          id="email" 
          name="email" 
          placeholder="your@virginia.edu" 
          required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input 
          type="password" 
          class="form-control" 
          id="password" 
          name="password" 
          placeholder="Enter password" 
          required>
      </div>

      <button type="submit" class="btn btn-primary">Log In</button>
    </form>

    <hr>

    <div class="mt-4">
      <p>Don't have an account? Register now!</p>
      <a class="btn btn-success" href="?command=home&register=1">Register</a> 
    </div>
  </main>

  <footer class="text-center py-3">
    <p>&copy; 2025 Hoo’s There Project</p>
  </footer>
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
