(() => {
    const body     = document.getElementById("friendsTableBody");
    const counter  = document.getElementById("friendCount");
    const modalEl  = document.getElementById("friendsModal");

    document.addEventListener("DOMContentLoaded", initFriendCount);

    async function initFriendCount() {
    const res = await fetch("index.php?command=get_friends");
    const { result, friends } = await res.json();
    if (result === "success") document.getElementById("friendCount").textContent = friends.length;
    }
  
    let cachedFriends = [];                    // local copy so we can mutate UI fast
  
    modalEl.addEventListener("show.bs.modal", loadFriends);
  
    async function loadFriends() {
      body.innerHTML = "<tr><td colspan='5' class='p-3 text-center'>Loading…</td></tr>";
  
      const res = await fetch("index.php?command=get_friends");
      const { result, friends } = await res.json();
      if (result !== "success") return;
  
      cachedFriends = friends;
  
      counter.textContent = friends.length;
      body.innerHTML = "";
      friends.forEach(f => body.appendChild(row(f)));
    }
  
    const row = f => {
      const tr   = document.createElement("tr");
      tr.innerHTML = `
        <td><img src="${f.avatar}" alt="" class="friend-avatar-xs"></td>
        <td><a class="link-primary link-underline-opacity-0" href="index.php?command=profile&id=${f.id}">${f.name}</a></td>
        <td>${f.year ?? "-"}</td>
        <td>${f.major ?? "-"}</td>
        <td class="text-end">
             <button class="btn btn-sm btn-danger">Remove</button>
        </td>`;
      tr.querySelector("button").onclick = () => removeFriend(f.id, tr);
      return tr;
    };
  
    async function removeFriend(id, tr) {
      const bodyData = new URLSearchParams({ id }).toString();
      const res  = await fetch("index.php?command=remove_friend",
                    { method:"POST",
                      headers:{ "Content-Type":"application/x-www-form-urlencoded"},
                      body: bodyData });
  
      const j = await res.json();
      if (j.result !== "success") { alert("Could not remove friend."); return; }
  
      // update UI instantly
      tr.remove();
      cachedFriends = cachedFriends.filter(x => x.id !== id);
      counter.textContent = cachedFriends.length;
    }
  
  })();