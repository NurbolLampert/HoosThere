/**
 * Helper: post to ?command=… and handle JSON response.
 */
async function postJSON(command, formData, onSuccess, onFailure) {
  const resp = await fetch(`index.php?command=${command}`, {
    method: "POST",
    body: formData
  });
  clearAlerts("social-alerts");
  if (!resp.ok) {
    createAlert("Server error. Try again.", "danger", "social-alerts");
    return;
  }
  const json = await resp.json();
  if (json.result === "success") {
    onSuccess(json);
  } else {
    onFailure(json);
  }
}

/** SOCIAL LINKS **/

async function updateSocialLinks() {
  const data = new FormData();
  data.append("instagram", document.getElementById("instagram").value);
  data.append("linkedin",  document.getElementById("linkedin").value);
  data.append("facebook",  document.getElementById("facebook").value);

  await postJSON(
    "update_social_links",
    data,
    () => createAlert("Social media links updated!", "success", "social-alerts"),
    (json) => createAlert(json.message || "Failed to update social media links!", "danger", "social-alerts")
  );
}

document.getElementById("social-links-form")
  ?.addEventListener("submit", e => {
    e.preventDefault();
    updateSocialLinks();
  });


/** PROFESSIONAL EXPERIENCES **/

async function addExperience() {
  const role = document.getElementById("new-exp-role").value.trim();
  const desc = document.getElementById("new-exp-desc").value.trim();
  if (!role || !desc) return;
  const data = new FormData();
  data.append("role", role);
  data.append("description", desc);

  await postJSON(
    "add_experience",
    data,
    () => { createAlert("Experience added!", "success", "social-alerts"); setTimeout(()=>location.reload(),500); },
    (json) => createAlert(json.message || "Failed to add experience.", "danger", "social-alerts")
  );
}

async function updateExperience(id) {
  const role = document.getElementById(`exp-role-${id}`).value;
  const desc = document.getElementById(`exp-desc-${id}`).value;
  const data = new FormData();
  data.append("id", id);
  data.append("role", role);
  data.append("description", desc);

  await postJSON(
    "update_experience",
    data,
    () => createAlert("Experience updated!", "success", "social-alerts"),
    (json) => createAlert(json.message || "Failed to update experience.", "danger", "social-alerts")
  );
}

document.getElementById("add-exp-form")
  ?.addEventListener("submit", e => {
    e.preventDefault();
    addExperience();
  });

document.querySelectorAll(".update-exp-btn")
  .forEach(btn => btn.addEventListener("click", () => {
    updateExperience(btn.dataset.id);
  }));


/** EDUCATION **/

async function addEducation() {
  const degree = document.getElementById("new-edu-degree").value.trim();
  const inst   = document.getElementById("new-edu-inst").value.trim();
  const grad   = document.getElementById("new-edu-grad").value.trim();
  if (!degree || !inst) return;
  const data = new FormData();
  data.append("degree", degree);
  data.append("institution", inst);
  data.append("expected_graduation", grad);

  await postJSON(
    "add_education",
    data,
    () => { createAlert("Education added!", "success", "social-alerts"); setTimeout(()=>location.reload(),500); },
    (json) => createAlert(json.message || "Failed to add education.", "danger", "social-alerts")
  );
}

async function updateEducation(id) {
  const degree = document.getElementById(`edu-degree-${id}`).value;
  const inst   = document.getElementById(`edu-inst-${id}`).value;
  const grad   = document.getElementById(`edu-grad-${id}`).value;
  const data = new FormData();
  data.append("id", id);
  data.append("degree", degree);
  data.append("institution", inst);
  data.append("expected_graduation", grad);

  await postJSON(
    "update_education",
    data,
    () => createAlert("Education updated!", "success", "social-alerts"),
    (json) => createAlert(json.message || "Failed to update education.", "danger", "social-alerts")
  );
}

document.getElementById("add-edu-form")
  ?.addEventListener("submit", e => {
    e.preventDefault();
    addEducation();
  });

document.querySelectorAll(".update-edu-btn")
  .forEach(btn => btn.addEventListener("click", () => {
    updateEducation(btn.dataset.id);
  }));


/** CLUBS & ORGS **/

async function addClub() {
  const name = document.getElementById("new-club-name").value.trim();
  const role = document.getElementById("new-club-role").value.trim();
  const year = document.getElementById("new-club-year").value.trim();
  if (!name) return;
  const data = new FormData();
  data.append("name", name);
  data.append("role", role);
  data.append("year", year);

  await postJSON(
    "add_club",
    data,
    () => { createAlert("Organization added!", "success", "social-alerts"); setTimeout(()=>location.reload(),500); },
    (json) => createAlert(json.message || "Failed to add organization.", "danger", "social-alerts")
  );
}

async function updateClub(id) {
  const name = document.getElementById(`club-name-${id}`).value;
  const role = document.getElementById(`club-role-${id}`).value;
  const year = document.getElementById(`club-year-${id}`).value;
  const data = new FormData();
  data.append("id", id);
  data.append("name", name);
  data.append("role", role);
  data.append("year", year);

  await postJSON(
    "update_club",
    data,
    () => createAlert("Organization updated!", "success", "social-alerts"),
    (json) => createAlert(json.message || "Failed to update organization.", "danger", "social-alerts")
  );
}

document.getElementById("add-club-form")
  ?.addEventListener("submit", e => {
    e.preventDefault();
    addClub();
  });

document.querySelectorAll(".update-club-btn")
  .forEach(btn => btn.addEventListener("click", () => {
    updateClub(btn.dataset.id);
  }));


/** VOLUNTEERING **/

async function addVolunteer() {
  const org  = document.getElementById("new-vol-org").value.trim();
  const desc = document.getElementById("new-vol-desc").value.trim();
  if (!org) return;
  const data = new FormData();
  data.append("organization", org);
  data.append("description", desc);

  await postJSON(
    "add_volunteer",
    data,
    () => { createAlert("Volunteer experience added!", "success", "social-alerts"); setTimeout(()=>location.reload(),500); },
    (json) => createAlert(json.message || "Failed to add volunteer experience.", "danger", "social-alerts")
  );
}

async function updateVolunteer(id) {
  const org  = document.getElementById(`vol-org-${id}`).value;
  const desc = document.getElementById(`vol-desc-${id}`).value;
  const data = new FormData();
  data.append("id", id);
  data.append("organization", org);
  data.append("description", desc);

  await postJSON(
    "update_volunteer",
    data,
    () => createAlert("Volunteer experience updated!", "success", "social-alerts"),
    (json) => createAlert(json.message || "Failed to update volunteer experience.", "danger", "social-alerts")
  );
}

document.getElementById("add-vol-form")
  ?.addEventListener("submit", e => {
    e.preventDefault();
    addVolunteer();
  });

document.querySelectorAll(".update-vol-btn")
  .forEach(btn => btn.addEventListener("click", () => {
    updateVolunteer(btn.dataset.id);
  }));
