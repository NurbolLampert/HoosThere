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
          src="<?=$this->getUserAvatar($user_id)?>"
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

    <!-- Academic history -->
    <section class="mt-4 section-card">
      <h2 class="fs-3">Academic History</h2>

      <div class="accordion" id="yearAccordion">
        <?php foreach ($acadData["grouped"] as $year=>$terms): ?>
          <div class="accordion-item">
            <h2 class="accordion-header" id="y<?=$year?>">
              <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#c<?=$year?>">
                Year <?=$year?>
              </button>
            </h2>
            <div id="c<?=$year?>" class="accordion-collapse collapse" data-bs-parent="#yearAccordion">
              <div class="accordion-body">
                <?php foreach ($terms as $term=>$records): ?>
                  <h5 class="fw-semibold"><?=$term?> Term</h5>
                  <ul class="list-group slim mb-3">
                  <?php foreach ($records as $rec): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?=$rec["course_code"]?> – <?=$rec["course_name"]?></span>
                        <span>
                          <span class="badge bg-info-subtle text-dark">
                            <?=number_format($rec["karma_avg"],3)?> / 10
                          </span>
                          <small class="text-muted">(<?=$rec["karma_votes"]?>)</small>

                          <?php if ($rec["viewer_is_teammate"]): ?>
                            <button class="btn btn-sm btn-outline-<?= $rec['viewer_rating']===null?'success':'secondary' ?> ms-2 rate-btn"
                                    data-rec="<?=$rec['id']?>"
                                    data-my="<?=$rec['viewer_rating'] ?? '' ?>">
                                <?= $rec['viewer_rating']===null ? 'Rate' : 'Update' ?>
                            </button>
                          <?php endif; ?>
                        </span>
                    </li>
                  <?php endforeach; ?>
                  </ul>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
   
    <!-- Mutual Friends Section -->
    <section class="mt-4 section-card">
      <h2 class="fs-3" id="mutual-friends-list-text">Mutual Friends (0)</h2>
      <div class="row" id="mutual-friends-list-row"></div>
    </section>
  </main>

  <footer class="text-center py-3">
    <p>&copy; 2025 Hoo’s There Project</p>
  </footer>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
  <script src="scripts/main.js"></script>
  <script src="scripts/mutual-friends-list.js"></script> 
  <script src="scripts/karma-rate.js"></script>
  <script>
    getMutualFriendsList();
  </script>

</body>
</html>