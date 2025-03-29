<!-- Names: Nurbol Lampert, Eric Weng -->
<!-- Project URL: https://cs4640.cs.virginia.edu/qgt7zm/project/index.html -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="authors" content="Your Name(s)">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hoo’s There - Home</title>
  <link 
    rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
  >
  <link rel="stylesheet" href="styles/main.css">
</head>
<body>
  <header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-md navbar-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="?command=home">Hoo's There</a>
        <button 
          class="navbar-toggler" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#navbarNav" 
          aria-controls="navbarNav" 
          aria-expanded="false" 
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="profile-view.html">Profile View</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="academics.html">Academics</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="social-professional-life.html">Social & Professional Life</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user-profile.html">User Profile</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- Navbar End -->
  </header>

  <main class="container my-4">
    <h1>Welcome to Hoo's There!</h1>
    <p>
      Log in or register to start connecting with your classmates.
    </p>

    <div class="row">
      <?=$this->showAlert()?>
    </div>

    <!-- Login Form -->
    <form action="?command=login" method="post">
      <div class="mb-3">
        <label for="name" class="form-label">Email</label>
        <input
          type="email" class="form-control" id="email" name="email"
          aria-describedby="emailHint" required
        >
        <div id="emailHint" class="form-text">
          Must be a UVA (@virginia.edu) email address.
        </div>
      </div>

      <div class="mb-3">
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

      <button type="submit" class="btn btn-primary">Log In</button>
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
