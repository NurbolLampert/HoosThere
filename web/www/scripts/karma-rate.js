document.addEventListener("click", ev => {
  if (!ev.target.matches(".rate-btn")) return;
  const recId = ev.target.dataset.rec;
  openModal(recId, ev.target);
});

function openModal(recId, anchorBtn){
  const modal = document.createElement("div");
  modal.className = "modal fade";
  modal.innerHTML = `
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body text-center">
        <label class="form-label">Give karma (0‑10)</label>
        <input type="range" min="0" max="10" value="5" id="slider" class="form-range">
        <div id="val" class="fw-bold mb-2">5</div>
        <button class="btn btn-primary w-100" id="submitKarma">Submit</button>
      </div>
    </div>
  </div>`;
  document.body.appendChild(modal);
  const bsModal = new bootstrap.Modal(modal);
  bsModal.show();
  const slider = modal.querySelector("#slider");
  const out    = modal.querySelector("#val");
  const current = anchor.dataset.my ? parseInt(anchor.dataset.my) : 5;
  slider.value = current;
  out.textContent = current;
  slider.oninput = () => out.textContent = slider.value;

  modal.querySelector("#submitKarma").onclick = async () => {
    const body = new URLSearchParams({ record_id:recId, points:slider.value }).toString();
    const r = await fetch("index.php?command=rate_project", {
        method:"POST",
        headers:{ "Content-Type":"application/x-www-form-urlencoded"},
        body});
    const j = await r.json();
    if (j.result === "success"){
        anchor.dataset.my = slider.value;
        anchor.classList.remove("btn-success","btn-secondary");
        anchor.classList.add("btn-secondary");
        anchor.textContent = "Update";
        anchor.parentNode.querySelector(".badge").textContent =
              Number(j.avg).toFixed(3)+" / 10";
        anchor.parentNode.querySelector("small").textContent =
              `(${j.n})`;
        bsModal.hide();
    } else alert(j.msg || "Could not save");
  };

  modal.addEventListener('hidden.bs.modal', () => modal.remove());
}
