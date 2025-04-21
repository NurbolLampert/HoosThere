document.addEventListener("DOMContentLoaded", () => loadRequests());

async function loadRequests() {
  const res = await fetch("index.php?command=get_friend_requests");
  const { result, requests } = await res.json();
  if (result !== "success" || !requests.length) return;

  document.getElementById("requestCount").textContent = requests.length;   // NEW
  const div = document.getElementById("friendRequestsBody");
  requests.forEach(req => div.appendChild(requestRow(req)));
}

function requestRow({ id, name, avatar, ago }) {
  const row = document.createElement("div");
  row.className = "d-flex align-items-center gap-2 mb-2";

  row.innerHTML = `
    <img src="${avatar}" alt="Avatar of ${name}" class="friend-pic-xxs">
    <span class="flex-grow-1">${name} <small class="text-muted">· ${ago} ago</small></span>
  `;

  ["accept","decline"].forEach(act => {
    const b = document.createElement("button");
    b.className = `btn btn-sm btn-${act==="accept"?"success":"outline-secondary"}`;
    b.textContent = act==="accept"?"Accept":"Decline";
    b.onclick = () => respond(id, act, row);
    row.appendChild(b);
  });
  return row;
}

async function respond(id, action, row) {
  const body = new URLSearchParams({ request_id:id, action }).toString();
  await fetch("index.php?command=act_on_request",
          { method:"POST", headers:{ "Content-Type":"application/x-www-form-urlencoded"}, body});
  row.remove();  
  const cnt = document.getElementById("requestCount");
  cnt.textContent = parseInt(cnt.textContent,10) - 1; 
  if (action === "accept") addFriendListRefresh(); 
}