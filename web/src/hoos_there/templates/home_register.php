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
    <p>
      Log in or register to start connecting with your classmates.
    </p>

    <div class="row">
      <?=$this->showAlert()?>
    </div>

    <h2>Create Account</h2>

    <!-- Register Form -->
    <form action="?command=register" method="post">    
      <div class="row mb-3">
        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Email</label>
          <input
            type="email" class="form-control" id="email" name="email"
            aria-describedby="emailHint" required
          >
          <div id="emailHint" class="form-text">
            Must be a UVA (@virginia.edu) email address.
          </div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Password</label>
          <input
            type="password" class="form-control" id="password" name="password"
            aria-describedby="passwordHint" required
          >
          <div id="passwordHint" class="form-text">
            Must be at least 8 characters long and contain at least one symbol.
            <!-- TODO more password requirements -->
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-success">Register</button>

      <hr>

      <!-- Switch to Register -->
      <p>Already have an account?</p>

      <a class="btn btn-primary" href="?command=home&register=0">Log In</a> 
    </form>
  </main>

  <footer class="text-center py-3">
    <p>&copy; 2025 Hoo’s There Project</p>
  </footer>
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
