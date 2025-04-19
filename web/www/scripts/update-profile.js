/**
 * Update the user's profile information.
 */
async function updateProfile() {
    // Create POST form
    let majorField = document.getElementById("major");
    let hometown = document.getElementById("hometown");
    let description = document.getElementById("description");

    let data = new FormData();
    data.append("major", majorField.value);
    data.append("hometown", hometown.value);
    data.append("description", description.value);

    // Submit to front controller
    var response = await fetch(
        "index.php?command=update_profile",
        {
            method: "POST",
            body: data
        }
    );

    clearAlerts();
    if (!response.ok) {
        console.log("Response failed");
        createAlert("Failed to update profile!", "danger");
    } else {
        var obj = await response.json();  
        onProfileUpdated(obj);
    }
}

/**
 * Notify the user that their profile was updated.
 */
function onProfileUpdated(data) {
    console.log(data);
    if (data.result === "success") {
        createAlert("Profile information updated!", "success");
    } else {
        createAlert("Failed to update profile!", "danger");
    }
}