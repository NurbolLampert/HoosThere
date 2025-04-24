var numMutualFriends = 0;

/**
 * Get the user's mutual friends list with the logged-in user.
 */
async function getMutualFriendsList() {
    // Get ID of other user
    const queryParams = new URLSearchParams(window.location.search);
    const userID = queryParams.get("id");

    var response = await new Promise(resolve => {
        var request = new XMLHttpRequest();
        request.open("GET", `./index.php?command=get_mutual_friends&id=${userID}`, true);
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
 * Show the user's mutual friends list on the document.
 */
function onGetFriendsList(response) {
    if (response.result !== "success") return;
    response.friends.forEach(friend => {
        addFriendItem(friend);
    });
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

    newFriendDiv.appendChild(friendImg);
    newFriendDiv.appendChild(friendLink);

    document.getElementById("mutual-friends-list-row").appendChild(newFriendDiv);

    // Update heading text
    numMutualFriends += 1;
    document.getElementById("mutual-friends-list-text").innerHTML = `Mutual Friends (${numMutualFriends})`;
}