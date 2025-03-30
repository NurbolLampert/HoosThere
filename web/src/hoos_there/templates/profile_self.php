<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="authors" content="Nurbol Lampert, Eric Weng">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile - Hoo’s There</title>
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
    <h1 class="fs-2">Your Profile</h1>

    <div class="row">
      <?=$this->showAlert()?>
    </div>

    <section class="row">
      <div class="col-4">
        <img
          src="profile-avatars/4m.jpg"
          alt="Your user avatar"
          class="profile-pic"
        >
      </div>

      <div class="col-8">
        <h2 class="fs-3">Gigachad Wolfgang</h2>
        <form>
          <div class="mb-3">
            <label for="profileBio" class="form-label"><strong>Description</strong></label>
            <textarea
              class="form-control" id="profileBio" rows="5"
              placeholder="This is your personal bio. You can describe yourself, your major, and your interests."
            ></textarea>
          </div>

          <div class="row mb-3 align-items-center">
            <div class="col-auto">
              <label for="profileHometown" class="form-label"><strong>City, State of Origin: </strong></label>
            </div>
            <div class="col">
              <input type="email" class="form-control" id="profileHometown" placeholder="Richmond, VA">
            </div>
          </div>

          <p><strong>Karma Score:</strong> 7.2</p>

          <button type="submit" class="btn btn-primary">Save Profile</button>
        </form>
      </div>
    </section>

    <!-- Friends Section -->
    <section>
      <h2 class="fs-3">Friends List</h2>
      <div class="row" id="friends-list-row">
          <div class="col-md-4 p-3 d-flex align-items-center friend-col">
            <img src="profile-avatars/2f.jpg" alt="Friend 1 avatar" class="friend-pic me-2">
            <a href="profile-view.html" class="link-primary link-underline-opacity-0 me-2">Erin</a>
            <button class="btn btn-danger btn-remove">Remove</button>
          </div>

          <div class="col-md-4 p-3 d-flex align-items-center friend-col">
            <img src="profile-avatars/5m.jpg" alt="Friend 2 avatar" class="friend-pic me-2">
            <a href="profile-view.html" class="link-primary link-underline-opacity-0 me-2">Nate</a>
            <button class="btn btn-danger btn-remove">Remove</button>
          </div>

          <div class="col-md-4 p-3 d-flex align-items-center friend-col">
            <img src="profile-avatars/4m.jpg" alt="Friend 3 avatar" class="friend-pic me-2">
            <a href="profile-view.html" class="link-primary link-underline-opacity-0 me-2">Lola</a>
            <button class="btn btn-danger btn-remove">Remove</button>
          </div>

          <div class="col-md-4 p-3 d-flex align-items-center friend-col">
            <img src="profile-avatars/3m.jpg" alt="Friend 4 avatar" class="friend-pic me-2">
            <a href="profile-view.html" class="link-primary link-underline-opacity-0 me-2">Jim</a>
            <button class="btn btn-danger btn-remove">Remove</button>
          </div>

          <div class="col-md-4 p-3 d-flex align-items-center friend-col">
            <img src="profile-avatars/1f.jpg" alt="Friend 5 avatar" class="friend-pic me-2">
            <a href="profile-view.html" class="link-primary link-underline-opacity-0 me-2">Mikey</a>
            <button class="btn btn-danger btn-remove">Remove</button>
          </div>
        </div>
    </section>

    <section>
      <h3 class="fs-4">Find a New Friend</h3>
      <form id="add-friend-form" action="#" method="post" aria-label="Add new friend form">
        <div class="mb-3">
          <label for="friendName" class="form-label">Name:</label>
          <input
            type="text"
            id="friendName"
            name="friendName"
            class="form-control"
            placeholder="Enter a friend's name"
            required
          >
        </div>
        <button type="submit" class="btn btn-primary">Add Friend</button>
      </form>
    </section>
  </main>

  <footer class="text-center py-3">
    <p>&copy; 2025 Hoo’s There Project</p>
  </footer>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>

  <!-- JavaScript to handle removing and adding friends -->
  <script
    src="scripts/friends-list.js"
  ></script>
</body>
</html>
