/**
 * Get the user's friends list.
 */
async function getFriendsList() {
    console.log("get friends");
    var response = await new Promise(resolve => {
        var request = new XMLHttpRequest();
        request.open("GET", "./index.php?command=get_friends", true);
        request.responseType = "json";
        request.send();

        request.addEventListener("load", function () {
            if (this.status == 200) {
                resolve(this.response);
            } else {
                console.log("Request failed.");
            }
        });

        request.addEventListener("error", function () {
            console.log("Request failed.");
        });
    });
    onGetFriendsList(response);
}

/**
 * Show the user's friends list on the document.
 */
function onGetFriendsList(response) {
    console.log(response);
    if (response.result !== "success") return;
}

/**
 * Add a new friend when submitting the form.
 */
function addNewFriend() {
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

    removeBtn.addEventListener('click', () => {
        // Each friend is in a .friend-col <div>, remove that
        const friendCol = removeBtn.closest('.friend-col');
        friendCol.remove();
    });

    newFriendDiv.appendChild(friendImg);
    newFriendDiv.appendChild(friendLink);
    newFriendDiv.appendChild(removeBtn);

    document.getElementById("friends-list-row").appendChild(newFriendDiv);

    friendNameInput.value = '';
}