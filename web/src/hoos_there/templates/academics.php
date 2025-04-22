<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="authors" content="Nurbol Lampert, Eric Weng">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Academics - Hoo’s There</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles/main.css">
  <style>
    .friend-avatar-xs{width:32px;height:32px;border-radius:50%;object-fit:cover}
    .friend-pic-xxs { width:24px; height:24px; border-radius:50%; object-fit:cover; }
  </style>
</head>
<body>
  <?php $this->showTemplate("navbar.php"); ?>
  <main class="container my-4">
    <h1 class="fs-2">Academic History</h1>
    <?=$this->showAlert()?>

    <!-- Outer Accordion by Year -->
    <div class="accordion" id="yearAccordion">
      <?php foreach ($academic_data["grouped"] as $year => $terms): ?>
        <!-- Year Accordion -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingYear<?=$year?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseYear<?=$year?>">
              Year <?=htmlspecialchars($year)?>
            </button>
          </h2>
          <div id="collapseYear<?=$year?>" class="accordion-collapse collapse" data-bs-parent="#yearAccordion">
            <div class="accordion-body">
              <div class="accordion" id="accordionYear<?=$year?>Terms">
                <?php foreach ($terms as $term => $records): ?>
                  <!-- Term Accordion -->
                  <div class="accordion-item">
                    <h3 class="accordion-header" id="heading<?=$year?><?=$term?>">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$year?><?=$term?>">
                        <?=$term?> Term
                      </button>
                    </h3>
                    <div id="collapse<?=$year?><?=$term?>" class="accordion-collapse collapse" data-bs-parent="#accordionYear<?=$year?>Terms">
                      <div class="accordion-body">
                        <ul style="list-style-type: none">
                          <?php foreach ($records as $record): ?>
                            <!-- Academic Record Item -->
                            <li>
                              <!-- Update Form -->
                              <div class="row g-2 mb-2 align-items-center">
                                <div class="col-auto">
                                  <form class="row g-2" method="post" action="?command=update_record">
                                    <input type="hidden" name="record_id" value="<?=htmlspecialchars($record["id"])?>">
                                    <div class="col-auto">
                                      <input name="course_code" class="form-control" value="<?=htmlspecialchars($record["course_code"] ?? "")?>">
                                    </div>
                                    <div class="col-auto">
                                      <input name="course_name" class="form-control" value="<?=htmlspecialchars($record["course_name"] ?? "")?>">
                                    </div>
                                    <div class="col-auto">
                                      <input name="project_title" class="form-control" value="<?=htmlspecialchars($record["project_title"] ?? "")?>" placeholder="Project Title">
                                    </div>
                                    <div class="col-auto d-flex align-items-center">
                                      <span class="badge bg-info-subtle text-dark">
                                            <?= number_format($record["karma_avg"],3) ?> / 10
                                      </span>
                                      <small class="text-muted ms-1">(<?= $record["karma_votes"] ?>)</small>
                                    </div>
                                    <div class="col-auto">
                                      <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                    </div>
                                  </form>
                                </div>

                                <!-- Delete Form -->
                                <div class="col-auto">
                                  <form method="post" action="?command=delete_record&id=<?=$record['id']?>">
                                    <input type="hidden" name="confirm" value="1">
                                    <button type="submit" class="mb-2 btn btn-outline-danger btn-sm">Delete</button>
                                  </form>
                                </div>
                              </div>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <!-- Accordions End -->

    <!-- Button to toggle Add Record form -->
    <div class="pt-3">
        <button class="btn btn-outline-success mb-3" id="add-record">+ Add New Record</button>
    </div>


    <!-- Add Record Form -->
    <form id="add-record-form" action="?command=add_record" method="post" class="border p-3 mb-4 d-none">
      <div class="row">
        <div class="col-md-2">
          <label for="year">Year</label>
          <input type="number" name="year" id="year" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label for="term">Term</label>
          <select name="term" id="term" class="form-control" required>
            <option value="">Select</option>
            <option value="Fall">Fall</option>
            <option value="J-Term">J-Term</option>
            <option value="Spring">Spring</option>
            <option value="Summer">Summer</option>
          </select>
        </div>
        <div class="col-md-2">
          <label for="course_code">Course Code</label>
          <input type="text" name="course_code" id="course_code" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label for="course_name">Course Name</label>
          <input type="text" name="course_name" id="course_name" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label for="project_title">Project Title (optional)</label>
          <input type="text" name="project_title" id="project_title" class="form-control">
        </div>
      </div>
      
      <!-- Pick Teammates -->
      <div class="row mt-2">
        <div class="col-md-6 position-relative teammate-search-parent">
          <label class="form-label">Teammates (optional)</label>
          <input type="text" id="newTeammateInput" class="form-control" placeholder="Lookup user…">
          <!-- Show selected teammates -->
          <div id="newTeammateTags" class="mt-1"></div>
          <input type="hidden" name="teammate_ids" id="newTeammateIds" value="">
        </div>
      </div>
      <button type="submit" class="btn btn-success btn-sm mt-3">Add Record</button>
    </form>

    <hr>

    <!-- Projects Section -->
    <section class="mt-4">
    <h2 class="fs-3">Projects</h2>

    <?php foreach ($academic_data["projects"] as $project): ?>
        <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?=htmlspecialchars($project["project_title"])?></h5>
            <p class="card-text"><?=htmlspecialchars($project["description"])?></p>

            <!-- Toggle Edit Form -->
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                    data-bs-target="#editProjectForm<?=$project['id']?>" aria-expanded="false" aria-controls="editProjectForm<?=$project['id']?>">
            Edit
            </button>

            <!-- Edit Form -->
            <div class="collapse mt-3" id="editProjectForm<?=$project['id']?>">
            <form method="post" action="?command=update_project&id=<?=$project['id']?>">
                <div class="mb-2">
                <label class="form-label visually-hidden" for="title<?=$project['id']?>">Title</label>
                <input type="text" class="form-control" id="title<?=$project['id']?>" name="project_title"
                        value="<?=htmlspecialchars($project["project_title"])?>" required>
                </div>
                <div class="mb-2">
                <label class="form-label visually-hidden" for="desc<?=$project['id']?>">Description</label>
                <textarea class="form-control" id="desc<?=$project['id']?>" name="project_description" rows="3"
                            required><?=htmlspecialchars($project["description"])?></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
            </form>

            <!-- Delete Form -->
            <form method="post" action="?command=delete_project&id=<?=$project['id']?>">
              <input type="hidden" name="confirm" value="1">
              <button type="submit" class="mt-2 btn btn-danger btn-sm">Delete Project</button>
            </form>
            </div>
        </div>
        </div>
    <?php endforeach; ?>

    <!-- Add New Project Form -->
    <button class="btn btn-outline-success mb-3" type="button" data-bs-toggle="collapse"
            data-bs-target="#addProjectForm" aria-expanded="false" aria-controls="addProjectForm">
         + Add New Project
    </button>

    <div class="collapse" id="addProjectForm">
        <form method="post" action="?command=add_project">
        <div class="mb-2">
            <label class="form-label visually-hidden" for="new_project_title">Title</label>
            <input type="text" class="form-control" id="new_project_title" name="project_title"
                placeholder="Project Title" required>
        </div>
        <div class="mb-2">
            <label class="form-label visually-hidden" for="new_project_description">Description</label>
            <textarea class="form-control" id="new_project_description" name="project_description" rows="3"
                    placeholder="Project Description" required></textarea>
        </div>
        <button type="submit" class="btn btn-success btn-sm">Add Project</button>
        </form>
    </div>
    </section>

    <hr>

    <!-- Future Goals Section -->
    <section class="mt-4">
      <h2 class="fs-3">Future Goals</h2>

      <?php $goals = htmlspecialchars($academic_data["goals"][0]["goal_description"] ?? '');?>

      <form method="post" action="?command=update_goals" class="mt-3">
          <div class="mb-3">
          <textarea
            name="goal_description" rows="3" class="form-control"
            placeholder="Describe your future goals here."><?=$goals?></textarea>
          </div>
          <button class="btn btn-outline-primary" type="submit">Save Goals</button>
      </form>
    </section>
  </main>

  <footer class="text-center py-3">
    <p>&copy; 2025 Hoo’s There Project</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="scripts/teammate-picker.js"></script>
  <script src="scripts/add-record-picker.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"
    integrity="sha512-+k1pnlgt4F1H8L7t3z95o3/KO+o78INEcXTbnoJQ/F2VqDVhWoaiVml/OEHv9HsVgxUaVW+IbiZPUJQfF/YxZw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"
  ></script>
  <script>
    $(document).ready(function() {
      $('button#add-record').on('click', function () {
        $('form#add-record-form').toggleClass('d-none')
      });
    });
  </script>
</body>
</html>
