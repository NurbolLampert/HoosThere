var numFriends = 0;

/**
 * Get the user's friends list.
 */
async function getFriendsList() {
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
    if (response.result !== "success") return;
    response.friends.forEach(friend => {
        addFriendItem(friend);
    });
}

/**
 * Add a new friend when submitting the form.
 */
async function addNewFriend() {
    const friendNameInput = document.getElementById('friendName');
    const friendName = friendNameInput.value.trim();
    friendNameInput.value = '';
    if (!friendName) return;

    clearAlerts("friend-alerts");
    var response = await new Promise(resolve => {
        var request = new XMLHttpRequest();
        request.open("POST", "./index.php?command=add_friend", true);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.responseType = "json";
        request.send(`name=${friendName}`);

        request.addEventListener("load", function () {
            if (this.status == 200) {
                resolve(this.response);
            } else {
                createAlert("Could not add that user as a friend.", "danger", "friend-alerts");
            }
        });

        request.addEventListener("error", function () {
            createAlert("Could not add that user as a friend.", "danger", "friend-alerts");
        });
    });
    onAddNewFriend(response);
}

/**
 * Update the user's friend list with the new friend.
 */
function onAddNewFriend(response) {
    if (response.result !== "success") {
        createAlert(response.message, "danger", "friend-alerts");
        return;
    } else {
        addFriendItem(response.friend);
        createAlert("Added a new friend. Start connecting!", "success", "friend-alerts");
    }
}

/**
 * Remove a friend from the lsit.
 */
async function removeFriend(button, friend) {
    // Each friend is in a .friend-col <div>, remove that
    const friendCol = button.closest('.friend-col');
    friendCol.remove();

    // Update heading text
    numFriends -= 1;
    document.getElementById("friends-list-text").innerHTML = `Friends List (${numFriends})`;

    clearAlerts("friend-alerts");
    var response = await new Promise(resolve => {
        var request = new XMLHttpRequest();
        request.open("POST", "./index.php?command=remove_friend", true);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.responseType = "json";
        request.send(`id=${friend.id}`);

        request.addEventListener("load", function () {
            if (this.status == 200) {
                resolve(this.response);
            } else {
                createAlert("Could not remove that friend.", "danger", "friend-alerts");
            }
        });

        request.addEventListener("error", function () {
            createAlert("Could not remove that friend.", "danger", "friend-alerts");
        });
    });
    // No callback needed
    onRemoveFriend(response, friend);
}

/**
 * Display a notification that a user was unfriended.
 */
function onRemoveFriend(response, friend) {
    if (response.result !== "success") {
        createAlert("Could not remove that friend.", "danger", "friend-alerts");
        return;
    } else {
        createAlert(`Removed friend ${friend.name}. No hard feelings!`, "success", "friend-alerts");
    }
}

/**
 * Display the given friend in the page.
 */
function addFriendItem(friend) {
    const newFriendDiv = document.createElement('div');
    newFriendDiv.classList.add('col-md-4', 'p-3', 'd-flex', 'align-items-center', 'friend-col');

    const friendImg = document.createElement('img');
    friendImg.src = friend.avatar;
    friendImg.alt = `Avatar of ${friend.name}`;
    friendImg.classList.add('friend-pic', 'me-2');

    const friendLink = document.createElement('a');
    friendLink.href = `index.php?command=profile&id=${friend.id}`;
    friendLink.classList.add('link-primary', 'link-underline-opacity-0', 'me-2');
    friendLink.textContent = friend.name;

    const removeBtn = document.createElement('button');
    removeBtn.classList.add('btn', 'btn-danger', 'btn-remove');
    removeBtn.type = 'button';
    removeBtn.textContent = 'Remove';

    removeBtn.addEventListener('click', function() {
        removeFriend(this, friend)
    });

    newFriendDiv.appendChild(friendImg);
    newFriendDiv.appendChild(friendLink);
    newFriendDiv.appendChild(removeBtn);

    document.getElementById("friends-list-row").appendChild(newFriendDiv);

    // Update heading text
    numFriends += 1;
    document.getElementById("friends-list-text").innerHTML = `Friends List (${numFriends})`;
}