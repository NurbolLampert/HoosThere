(() => {
    const inputs = document.querySelectorAll(".teammate-input");
    inputs.forEach(inp => attachPicker(inp));
  
    function attachPicker(inp){
      let timer, recordId = inp.dataset.record;
      const tagWrap = document.getElementById(`tags${recordId}`);
      const hidden  = document.getElementById(`teammatesField${recordId}`);
      const chosen  = new Map();
  
      // auto‑complete popper
      const list = document.createElement("ul");
      list.className = "list-group position-absolute w-100";
      list.style.zIndex = 1000;
      list.hidden = true;
      const parent = inp.closest(".teammate-search-parent");
      parent.appendChild(list);  
      inp.addEventListener("input", () => {
        clearTimeout(timer);
        const q = inp.value.trim();
        if (!q){ list.hidden = true; return; }
        timer = setTimeout(() => search(q), 250);
      });
  
      async function search(q){
        const r = await fetch(`index.php?command=search_users&q=${encodeURIComponent(q)}`);
        const { result, matches } = await r.json();
        if (result !== "success") return;
        list.innerHTML = "";
        matches.forEach(m => {
            const li = document.createElement("li");
            const already = chosen.has(m.id);
            li.className = "list-group-item d-flex align-items-center gap-2" +
                           (already ? " disabled" : "");
            li.innerHTML = `
               <img src="${m.avatar}" class="friend-avatar-xs">
               <span>${m.name}</span>`;
            if (!already) li.onclick = () => addTeammate(m);
            list.appendChild(li);
          });          
        list.hidden = list.childElementCount === 0;
      }
  
      function addTeammate(user){
        list.hidden = true;
        inp.value   = "";
        chosen.set(user.id, user.name);
        renderTags();
        hidden.value = [...chosen.keys()].join(",");
      }
  
      function renderTags(){
        tagWrap.innerHTML = "";
        chosen.forEach((name,id) => {
          const span = document.createElement("span");
          span.className = "badge bg-secondary me-1";
          span.textContent = name + " ✕";
          span.style.cursor = "pointer";
          span.onclick = () => { chosen.delete(id); renderTags(); hidden.value=[...chosen.keys()].join(","); };
          tagWrap.appendChild(span);
        });
      }
    }
  })();
  