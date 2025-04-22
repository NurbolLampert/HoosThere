<div class="accordion" id="yearAccordion">
  <?php foreach ($academic_data["grouped"] as $year => $terms): ?>
  <!-- Year Accordion -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingYear<?=$year?>">
      <button class="accordion-button collapsed" type="button"
        data-bs-toggle="collapse" data-bs-target="#collapseYear<?=$year?>">
        Year <?=htmlspecialchars($year)?>
      </button>
    </h2>
    
    <div id="collapseYear<?=$year?>" class="accordion-collapse collapse"
      data-bs-parent="#yearAccordion">
      <div class="accordion-body">
        <div class="accordion" id="accordionYear<?=$year?>Terms">
        <?php foreach ($terms as $term => $records): ?>
          <!-- Term Accordion -->
          <div class="accordion-item">
          <h3 class="accordion-header" id="heading<?=$year?><?=$term?>">
            <button class="accordion-button collapsed" type="button"
            data-bs-toggle="collapse" data-bs-target="#collapse<?=$year?><?=$term?>">
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
                      <input type="hidden" name="record_id"
                        value="<?=htmlspecialchars($record["id"])?>">
                      <div class="col-auto">
                        <input name="course_code" class="form-control"
                          value="<?=htmlspecialchars($record["course_code"] ?? "")?>">
                      </div>
                      <div class="col-auto">
                        <input name="course_name" class="form-control"
                          value="<?=htmlspecialchars($record["course_name"] ?? "")?>">
                      </div>
                      <div class="col-auto">
                        <input name="project_title" class="form-control"
                         value="<?=htmlspecialchars($record["project_title"] ?? "")?>" placeholder="Project Title">
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