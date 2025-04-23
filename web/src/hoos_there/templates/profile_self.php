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

          <p id="profileKarma"><strong>Karma Score:</strong>
            <span class="avg"><?= number_format($user["karma_avg"],3) ?></span> / 10
            <small class="text-muted">(<span class="votes"><?= $user["karma_votes"] ?></span>)</small>
          </p>

          <button type="submit" class="btn btn-primary" onclick="updateProfile(); return false">Save Profile</button>
        </form>
      </div>
    </section>

    <section class="mt-4 section-card">
      <h2 class="fs-3 d-inline me-2">
          Friends: <span id="friendCount">0</span>
      </h2>

      <button class="btn btn-sm btn-outline-primary"
              id="openFriendsBtn"
              data-bs-toggle="modal"
              data-bs-target="#friendsModal">
          View Friends
      </button>

      <h3 class="fs-5 mt-3">Look up Hoos</h3>
      <input type="search"
            id="userSearch"
            class="form-control mb-2"
            placeholder="Type a name…">
      <ul id="searchResults" class="list-group slim"></ul>
    </section>

    <div class="modal fade" id="friendsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Your Friends</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-0">
            <table class="table table-sm align-middle mb-0 tr-hover">
              <thead class="table-light">
                <tr>
                  <th scope="col"></th>
                  <th>Name</th>
                  <th>Grad Year</th>
                  <th>Major</th>
                  <th class="text-end"></th>
                </tr>
              </thead>
              <tbody id="friendsTableBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <section class="mt-4 section-card">
      <h3 class="fs-4">
        Friend Requests
        <span class="badge bg-secondary badge-count" id="requestCount">0</span>
        <button class="btn btn-link p-0 ms-1" data-bs-toggle="collapse" data-bs-target="#reqCollapse">
          show / hide
        </button>
      </h3>
      <div id="reqCollapse" class="collapse show">
        <div id="friendRequestsBody" class="mt-2"></div>
      </div>
    </section>
  </main>

  <footer class="text-center py-3">
    <p>&copy; 2025 Hoo’s There Project</p>
  </footer>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
  <script src="scripts/main.js"></script>
  <script src="scripts/update-profile.js"></script>
  <script src="scripts/friends-list.js"></script>  
  <script src="scripts/user-search.js"></script>
  <script src="scripts/friend-requests.js"></script>
  <script src="scripts/friends-modal.js"></script>
  <script>
    getFriendsList();
  </script>
  <style>
    .friend-pic-sm {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;   
    }
    .friend-avatar-xs { 
      width:40px;
      height:40px;
      border-radius:50%;
      object-fit:cover; 
    }
  </style>
</body>
</html>
