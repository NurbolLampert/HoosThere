<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="authors" content="Nurbol Lampert, Eric Weng">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hoo’s There Profile View</title>
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
    <?php
      $user = $this->getUserInfo($user_id);
    ?>

    <h1 class="fs-2"><?=$user["name"]?>'s Profile</h1>

    <div class="row">
      <?=$this->showAlert()?>
    </div>

    <section class="row">
      <div class="col-4">
        <img
          src="profile-avatars/3m.jpg"
          alt="Other user avatar"
          class="profile-pic"
        >
      </div>

      <div class="col-8">
        <h2 class="fs-3"><?=$user["name"]?></h2>
        <p><strong>Graduating Year:</strong> <?=$user["year"]?></p>
        <p><strong>Major:</strong> <?=$user["major"]?></p>
        <p><strong>Hometown:</strong> <?=$user["hometown"]?></p>
        <p><strong>Description:</strong></p>
        <p><?=$user["description"]?></p>
        <p><strong>Karma Score:</strong> 8.5</p>
      </div>
    </section>
   
    <!-- Friends Section -->
    <section>
      <h2 class="fs-3">Mutual Friends</h2>

      <div class="row">
        <div class="col-md-4 p-3">
          <img
            src="profile-avatars/1f.jpg"
            alt="Mutual friend 1 avatar"
            class="friend-pic"
          >
          <a href="profile-view.html" class="link-primary link-underline-opacity-0">Ashley</a>
          <!-- Link to friend's profile -->
        </div>

        <div class="col-md-4 p-3">
          <img
            src="profile-avatars/5m.jpg"
            alt="Mutual friend 2 avatar"
            class="friend-pic"
          >
          <a href="profile-view.html" class="link-primary link-underline-opacity-0">Jamal</a>
        </div>

        <div class="col-md-4 p-3">
          <img
            src="profile-avatars/2f.jpg"
            alt="Mutual friend 3 avatar"
            class="friend-pic"
          >
          <a href="profile-view.html" class="link-primary link-underline-opacity-0">Sofia</a>
        </div>
      </div>
    </section>
  </main>

  <footer class="text-center py-3">
    <p>&copy; 2025 Hoo’s There Project</p>
  </footer>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>