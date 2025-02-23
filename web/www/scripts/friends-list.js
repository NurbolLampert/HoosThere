// Remove friend from list
function attachRemoveHandler(button) {
    button.addEventListener('click', () => {
        // Each friend is in a .friend-col <div>, remove that
        const friendCol = button.closest('.friend-col');
        friendCol.remove();
    });
}

// Grab all existing remove buttons
const existingRemoveButtons = document.querySelectorAll('.btn-remove');
existingRemoveButtons.forEach(btn => attachRemoveHandler(btn));

const addFriendForm = document.getElementById('add-friend-form');
const friendsListRow = document.getElementById('friends-list-row');

// Add a new friend when submitting the form
addFriendForm.addEventListener('submit', (event) => {
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

    friendsListRow.appendChild(newFriendDiv);

    friendNameInput.value = '';
});