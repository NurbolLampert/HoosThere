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

  <script src="scripts/main.js"></script>
  <script src="scripts/update-profile.js"></script>
  <script src="scripts/friends-list.js"></script>  
</head>
<body>
  <?php
    $this->showTemplate("navbar.php");
  ?>

  <main class="container my-4">
    <h1 class="fs-2">Your Profile</h1>

    <div class="row" id="alerts">
      <?=$this->showAlert()?>
    </div>

    <?php
      $user = $this->getUserInfo($user_id);
    ?>

    <section class="row">
      <div class="col-4">
        <img
          src="<?=$this->getUserAvatar($user_id)?>"
          alt="Your user avatar"
          class="profile-pic"
        >
      </div>

      <div class="col-8">
        <h2 class="fs-3"><?=$user["name"]?></h2>

        <!-- Profile Form -->
        <form>
          <p><strong>Graduating Year:</strong> <?=$user["year"]?></p>
          
          <div class="row mb-3 align-items-center">
            <div class="col-auto">
              <label for="major" class="form-label"><strong>Major: </strong></label>
            </div>
            <div class="col">
              <input type="text" class="form-control" name="major" id="major" placeholder="Subject"
              value="<?=$user["major"]?>">
            </div>
          </div>
          
          <div class="row mb-3 align-items-center">
            <div class="col-auto">
              <label for="hometown" class="form-label"><strong>Hometown: </strong></label>
            </div>
            <div class="col">
              <input type="text" class="form-control" name="hometown" id="hometown" placeholder="City, ST"
              value="<?=$user["hometown"]?>">
            </div>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label"><strong>Description:</strong></label>
            <textarea
              class="form-control" name="description" id="description" rows="5"
              placeholder="This is your personal bio. You can describe yourself, your major, and your interests."
            ><?=$user["description"]?></textarea>
          </div>

          <p><strong>Karma Score:</strong> 7.2</p>

          <button type="submit" class="btn btn-primary" onclick="updateProfile(); return false">Save Profile</button>
        </form>
      </div>
    </section>

    <!-- Friends Section -->
    <section>
      <h2 class="fs-3" id="friends-list-text">Friends List</h2>
      <div class="row" id="friends-list-row"></div>
    </section>

    <section>
      <h3 class="fs-4">Find a New Friend</h3>

      <div class="row" id="friend-alerts"></div>

      <form id="add-friend-form" aria-label="Add new friend form">
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

        <button type="submit" class="btn btn-primary" onclick="addNewFriend(); return false">Add Friend</button>
      </form>
    </section>
  </main>

  <footer class="text-center py-3">
    <p>&copy; 2025 Hoo’s There Project</p>
  </footer>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
  <script>
    getFriendsList();
  </script>
</body>
</html>
