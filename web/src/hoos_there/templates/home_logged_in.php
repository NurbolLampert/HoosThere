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
      Explore your community using the links above.
    </p>

    <div class="row">
      <?=$this->showAlert()?>
    </div>

    <?php
      $user = $this->getUserInfo();
    ?>

    <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <h2>Account Actions</h2>
            <a class="btn btn-primary" href="?command=profile&id=<?=$user["id"]?>">Edit Profile</a> 
            <a class="btn btn-secondary" href="?command=user_data">Request Data</a> 
          </div>

        <div class="mb-3">
          <h2>User Logout</h2>

          <!-- Show current user -->
          <p>Currently logged in as <?=$user["name"]?> (<?=$user["email"]?>).<p>

          <!-- Logout Button -->
          <a class="btn btn-danger" href="?command=logout">Log Out</a> 
        </div>
      </div>

      <!-- Show recently registered users -->
      <div class="col-md-6">
        <h2>New Users</h2>

        <ul>
          <?php
            $users = $this->getNewUsers();
            foreach ($users as $user) {
              echo "<li>";
              echo '<a href="?command=profile&id=' . $user["id"] . '">';
              echo $user["name"];
              echo "</a>";
              echo "</li>";
            }
          ?>
        </ul>
      </div>
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
