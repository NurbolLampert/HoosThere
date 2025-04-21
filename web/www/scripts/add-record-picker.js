(() => {
    const inp   = document.getElementById("newTeammateInput");
    const tags  = document.getElementById("newTeammateTags");
    const hid   = document.getElementById("newTeammateIds");
    const added = new Map();
  
    let list = createDropdown();
  
    inp.parentNode.appendChild(list);
  
    inp.addEventListener("input", () => {
      const q = inp.value.trim();
      if (!q){ list.hidden = true; return; }
      debounceSearch(q);
    });
  
    const debounceSearch = debounce(async q => {
      const r = await fetch(`index.php?command=search_users&q=${encodeURIComponent(q)}`);
      const { result, matches } = await r.json();
      if (result !== "success") return;
      list.innerHTML = "";
      matches.forEach(u => {
        if (added.has(u.id)) return;
        const li = document.createElement("li");
        li.className = "list-group-item list-group-item-action d-flex align-items-center gap-2";
        li.innerHTML = `<img src="${u.avatar}" class="friend-pic-xxs"> <span>${u.name}</span>`;
        li.onclick = () => addUser(u);
        list.appendChild(li);
      });
      list.hidden = !list.childElementCount;
    },300);
  
    function addUser(u){
      list.hidden = true; inp.value = "";
      added.set(u.id, u);
      hid.value = [...added.keys()].join(",");
      renderTags();
    }
  
    function renderTags(){
      tags.innerHTML = "";
      added.forEach(u => {
        const span = document.createElement("span");
        span.className = "badge bg-secondary me-1";
        span.style.cursor = "pointer";
        span.innerHTML = `<img src="${u.avatar}" class="friend-pic-xxs me-1">${u.name} ×`;
        span.onclick = () => { added.delete(u.id); hid.value=[...added.keys()].join(","); renderTags(); };
        tags.appendChild(span);
      });
    }
  
    function createDropdown(){
      const ul = document.createElement("ul");
      ul.className = "list-group position-absolute w-100";
      ul.style.zIndex = 1000;
      ul.hidden = true;
      return ul;
    }
  
    function debounce(fn,ms){
      let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); };
    }
  })();
  