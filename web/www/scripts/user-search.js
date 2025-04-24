(() => {
    const ul = document.getElementById("searchResults");
    const inp = document.getElementById("userSearch");
    let timer;                                    
  
    inp.addEventListener("input", e => {
      clearTimeout(timer);
      timer = setTimeout(() => doSearch(inp.value.trim()), 300);
    });
  
    async function doSearch(term) {
      ul.innerHTML = "";                          // clear old list
      if (!term) return;
  
      const res = await fetch(`index.php?command=search_users&q=${encodeURIComponent(term)}`);
      if (!res.ok) return;
  
      const { result, matches } = await res.json();
      if (result !== "success") return;
  
      matches.forEach(m => ul.appendChild(resultItem(m)));
    }
  
    const resultItem = ({ id, name, avatar, is_friend }) => {
      // arrow fn returns <li>
      const li = document.createElement("li");
      li.className = "list-group-item d-flex align-items-center gap-2";
  
      li.innerHTML = `
        <img src="${avatar}" alt="Avatar of ${name}" class="friend-pic-xxs">
        <a class="flex-grow-1 link-underline-opacity-0" href="index.php?command=profile&id=${id}">${name}</a>
      `;
  
      if (is_friend) {
        li.insertAdjacentHTML("beforeend",
          '<span class="badge bg-success">Friends</span>');
      } else {
        const btn = document.createElement("button");
        btn.className = "btn btn-sm btn-primary";
        btn.textContent = "Send Request";
        btn.onclick = () => sendRequest(id, name, btn);
        li.appendChild(btn);
      }
      return li;
    };
  
    async function sendRequest(id, li, btn){
        const body = new URLSearchParams({ id }).toString();
        const res  = await fetch("index.php?command=send_friend_request",
                     { method:"POST", headers:{ "Content-Type":"application/x-www-form-urlencoded"}, body});
        const j = await res.json();
        if (j.result === "success") {
            btn.replaceWith(badge("Requested"));
        } else alert("Could not send request.");
      }
  
    const badge = text => {
      const s = document.createElement("span");
      s.className = "badge bg-secondary";
      s.textContent = text;
      return s;
    };
  })();
  