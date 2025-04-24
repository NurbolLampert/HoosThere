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
    .card-header-custom {
      background-color: #f8f9fa;
      font-weight: bold;
      border-bottom: 1px solid #dee2e6;
    }
    .form-section {
      margin-bottom: 2rem;
    }
    .accordion .accordion-item {
      border: none;
      border-bottom: 1px solid #dee2e6;
    }
  </style>
  <script src="scripts/main.js"></script>
  <script src="scripts/social-professional.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"
    integrity="sha512-+k1pnlgt4F1H8L7t3z95o3/KO+o78INEcXTbnoJQ/F2VqDVhWoaiVml/OEHv9HsVgxUaVW+IbiZPUJQfF/YxZw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"
  ></script>
  <script>
    $(document).ready(function() {
      $('#update-socials-form').on('submit', function () {
        updateSocialLinks();
        return false;
      });

      $('#add-exp-form').on('submit', function () {
        addExperience();
        return false;
      });

      $("button.update-exp-btn").on('click', function () {
        updateExperience($(this).dataset.id);
      });

      $("#add-edu-form").on('submit', function () {
        addEducation();
        return false;
      });
        
      $("button.update-edu-btn").on('click', function () {
        updateEducation($(this).dataset.id);
      });

      $("#add-club-form").on('submit', function () {
        addClub();
        return false;
      });
        
      $("button.update-club-btn").on('click', function () {
        updateClub($(this).dataset.id);
      });

      $("#add-vol-form").on('submit', function () {
        addVolunteer();
        return false;
      });
        
      $("button.update-vol-btn").on('click', function () {
        updateVolunteer($(this).dataset.id);
      });
    });
  </script>
</head>
<body>
  <?php $this->showTemplate("navbar.php"); ?>
  <main class="container my-5">

    <div id="social-alerts">
      <?=$this->showAlert()?>
    </div>
    
    <!-- Social Media Section -->
    <section class="form-section">
      <div class="card shadow-sm">
        <div class="card-header card-header-custom">
          <h2 class="h4 mb-0">Social Media</h2>
        </div>
        <div class="card-body">
          <form id="update-socials-form">
            <div class="row g-3">
              <div class="col-md-4">
                <label for="instagram" class="form-label">Instagram</label>
                <input type="text" name="instagram" id="instagram" class="form-control"
                       value="<?= htmlspecialchars($data["social"]["instagram"] ?? '') ?>">
              </div>
              <div class="col-md-4">
                <label for="linkedin" class="form-label">LinkedIn</label>
                <input type="text" name="linkedin" id="linkedin" class="form-control"
                       value="<?= htmlspecialchars($data["social"]["linkedin"] ?? '') ?>">
              </div>
              <div class="col-md-4">
                <label for="facebook" class="form-label">Facebook</label>
                <input type="text" name="facebook" id="facebook" class="form-control"
                       value="<?= htmlspecialchars($data["social"]["facebook"] ?? '') ?>">
              </div>
            </div>
            <div class="mt-3">
              <button class="btn btn-primary" type="submit">Update Links</button>
            </div>
          </form>
        </div>
      </div>
    </section>
    
    <hr class="my-5">
    
    <!-- Professional Life Accordion -->
    <section class="form-section">
      <div class="card shadow-sm">
        <div class="card-header card-header-custom">
          <h2 class="h4 mb-0">Professional Life</h2>
        </div>
        <div class="card-body">
          <div class="accordion" id="professionalAccordion">
            <!-- Experiences Section -->
            <div class="accordion-item">
              <h3 class="accordion-header" id="headingExperiences">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseExperiences" aria-expanded="true"
                        aria-controls="collapseExperiences">
                  Experiences
                </button>
              </h3>
              <div id="collapseExperiences" class="accordion-collapse collapse show"
                   aria-labelledby="headingExperiences" data-bs-parent="#professionalAccordion">
                <div class="accordion-body">
                  <ul class="list-group mb-3">
                    <?php foreach ($data["experiences"] ?? [] as $exp): ?>
                      <li class="list-group-item">
                        <form method="post" action="?command=update_experience" class="row g-2 align-items-center">
                          <input type="hidden" name="id" value="<?= $exp["id"] ?>">
                          <div class="col-md-5">
                            <input type="text" name="role" class="form-control" placeholder="Role"
                                   value="<?= htmlspecialchars($exp["role"] ?? '') ?>">
                          </div>
                          <div class="col-md-5">
                            <input type="text" name="description" class="form-control" placeholder="Description"
                                   value="<?= htmlspecialchars($exp["description"] ?? '') ?>">
                          </div>
                          <div class="col-md-2">
                            <button class="btn btn-sm btn-outline-primary w-100" type="submit">Save</button>
                          </div>
                        </form>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <!-- Form to add a new experience -->
                  <form method="post" action="?command=add_experience" class="row g-2 align-items-center">
                    <div class="col-md-5">
                      <input type="text" name="role" class="form-control" placeholder="New Experience Role">
                    </div>
                    <div class="col-md-5">
                      <input type="text" name="description" class="form-control" placeholder="New Experience Description">
                    </div>
                    <div class="col-md-2">
                      <button class="btn btn-outline-success w-100" type="submit">Add Experience</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
    
            <!-- Education Section -->
            <div class="accordion-item">
              <h3 class="accordion-header" id="headingEducation">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseEducation" aria-expanded="false"
                        aria-controls="collapseEducation">
                  Education
                </button>
              </h3>
              <div id="collapseEducation" class="accordion-collapse collapse"
                   aria-labelledby="headingEducation" data-bs-parent="#professionalAccordion">
                <div class="accordion-body">
                  <ul class="list-group mb-3">
                    <?php foreach ($data["education"] ?? [] as $edu): ?>
                      <li class="list-group-item">
                        <form method="post" action="?command=update_education" class="row g-2 align-items-center">
                          <input type="hidden" name="id" value="<?= $edu["id"] ?>">
                          <div class="col-md-4">
                            <input type="text" name="degree" class="form-control" placeholder="Degree"
                                   value="<?= htmlspecialchars($edu["degree"] ?? '') ?>">
                          </div>
                          <div class="col-md-4">
                            <input type="text" name="institution" class="form-control" placeholder="Institution"
                                   value="<?= htmlspecialchars($edu["institution"] ?? '') ?>">
                          </div>
                          <div class="col-md-2">
                            <input type="text" name="expected_graduation" class="form-control" placeholder="Graduation"
                                   value="<?= htmlspecialchars($edu["expected_graduation"] ?? '') ?>">
                          </div>
                          <div class="col-md-2">
                            <button class="btn btn-sm btn-outline-primary w-100" type="submit">Save</button>
                          </div>
                        </form>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <!-- Form to add a new education record -->
                  <form method="post" action="?command=add_education" class="row g-2 align-items-center">
                    <div class="col-md-4">
                      <input type="text" name="degree" class="form-control" placeholder="Degree">
                    </div>
                    <div class="col-md-4">
                      <input type="text" name="institution" class="form-control" placeholder="Institution">
                    </div>
                    <div class="col-md-2">
                      <input type="text" name="expected_graduation" class="form-control" placeholder="Graduation">
                    </div>
                    <div class="col-md-2">
                      <button class="btn btn-outline-success w-100" type="submit">Add Education</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div> <!-- End Accordion -->
        </div>
      </div>
    </section>
    
    <hr class="my-5">
    
    <!-- Clubs and Organizations Accordion -->
    <section class="form-section">
      <div class="card shadow-sm">
        <div class="card-header card-header-custom">
          <h2 class="h4 mb-0">Clubs and Organizations</h2>
        </div>
        <div class="card-body">
          <div class="accordion" id="clubsAccordion">
            <!-- Student Organizations Section -->
            <div class="accordion-item">
              <h3 class="accordion-header" id="headingClubs">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseClubs" aria-expanded="true"
                        aria-controls="collapseClubs">
                  Student Organizations
                </button>
              </h3>
              <div id="collapseClubs" class="accordion-collapse collapse show"
                   aria-labelledby="headingClubs" data-bs-parent="#clubsAccordion">
                <div class="accordion-body">
                  <ul class="list-group mb-3">
                    <?php foreach ($data["clubs"] ?? [] as $club): ?>
                      <li class="list-group-item">
                        <form method="post" action="?command=update_club" class="row g-2 align-items-center">
                          <input type="hidden" name="id" value="<?= $club["id"] ?>">
                          <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="Organization Name"
                                   value="<?= htmlspecialchars($club["name"] ?? '') ?>">
                          </div>
                          <div class="col-md-3">
                            <input type="text" name="role" class="form-control" placeholder="Role"
                                   value="<?= htmlspecialchars($club["role"] ?? '') ?>">
                          </div>
                          <div class="col-md-3">
                            <input type="text" name="year" class="form-control" placeholder="Year"
                                   value="<?= htmlspecialchars($club["year"] ?? '') ?>">
                          </div>
                          <div class="col-md-2">
                            <button class="btn btn-sm btn-outline-primary w-100" type="submit">Save</button>
                          </div>
                        </form>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <!-- Form to add a new organization -->
                  <form method="post" action="?command=add_club" class="row g-2 align-items-center">
                    <div class="col-md-4">
                      <input type="text" name="name" class="form-control" placeholder="Organization Name">
                    </div>
                    <div class="col-md-3">
                      <input type="text" name="role" class="form-control" placeholder="Role">
                    </div>
                    <div class="col-md-3">
                      <input type="text" name="year" class="form-control" placeholder="Year">
                    </div>
                    <div class="col-md-2">
                      <button class="btn btn-outline-success w-100" type="submit">Add Organization</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
    
            <!-- Volunteering Section -->
            <div class="accordion-item">
              <h3 class="accordion-header" id="headingVolunteering">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseVolunteering" aria-expanded="false"
                        aria-controls="collapseVolunteering">
                  Volunteering
                </button>
              </h3>
              <div id="collapseVolunteering" class="accordion-collapse collapse"
                   aria-labelledby="headingVolunteering" data-bs-parent="#clubsAccordion">
                <div class="accordion-body">
                  <ul class="list-group mb-3">
                    <?php foreach ($data["volunteering"] ?? [] as $vol): ?>
                      <li class="list-group-item">
                        <form method="post" action="?command=update_volunteer" class="row g-2 align-items-center">
                          <input type="hidden" name="id" value="<?= $vol["id"] ?>">
                          <div class="col-md-5">
                            <input type="text" name="organization" class="form-control" placeholder="Organization"
                                   value="<?= htmlspecialchars($vol["organization"] ?? '') ?>">
                          </div>
                          <div class="col-md-5">
                            <input type="text" name="description" class="form-control" placeholder="Description"
                                   value="<?= htmlspecialchars($vol["description"] ?? '') ?>">
                          </div>
                          <div class="col-md-2">
                            <button class="btn btn-sm btn-outline-primary w-100" type="submit">Save</button>
                          </div>
                        </form>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <!-- Form to add a new volunteering experience -->
                  <form method="post" action="?command=add_volunteer" class="row g-2 align-items-center">
                    <div class="col-md-5">
                      <input type="text" name="organization" class="form-control" placeholder="Organization">
                    </div>
                    <div class="col-md-5">
                      <input type="text" name="description" class="form-control" placeholder="Description">
                    </div>
                    <div class="col-md-2">
                      <button class="btn btn-outline-success w-100" type="submit">Add Volunteering</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
  </main>

  <footer class="text-center py-3">
    <p>&copy; 2025 Hoo’s There Project</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
