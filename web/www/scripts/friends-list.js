$(document).ready(function () {
    // Add event handlers to all "remove" buttons
    $("button.btn-ready").on("click", () => {
        // Remove friend from list
        // Each friend is in a .friend-col <div>
        const friendCol = $(this).closest('div.friend-col');
        friendCol.remove();
    });

    // Add a new friend when submitting the form
    $("#add-friend-form").on('submit', (event) => {
        event.preventDefault();
        const friendNameInput = document.getElementById('friendName');
        const newFriendName = friendNameInput.value.trim();
        if (!newFriendName) return;
    
        const newFriendDiv = document.createElement('div');
        newFriendDiv.classList.add('col-md-4', 'p-3', 'd-flex', 'align-items-center', 'friend-col');
    
        const friendImg = document.createElement('img');
        friendImg.src = 'profile-avatars/3m.jpg';
        friendImg.alt = 'friend avatar';
        friendImg.classList.add('friend-pic', 'me-2');
    
        const friendLink = document.createElement('a');
        friendLink.href = '#';
        friendLink.classList.add('link-primary', 'link-underline-opacity-0', 'me-2');
        friendLink.textContent = newFriendName;
    
        const removeBtn = document.createElement('button');
        removeBtn.classList.add('btn', 'btn-danger', 'btn-remove');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Remove';
    
        attachRemoveHandler(removeBtn);
    
        newFriendDiv.appendChild(friendImg);
        newFriendDiv.appendChild(friendLink);
        newFriendDiv.appendChild(removeBtn);
    
        $("#friends-list-row").appendChild(newFriendDiv);
    
        friendNameInput.value = '';
    });
});