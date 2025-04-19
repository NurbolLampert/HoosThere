/**
 * Update the user's social media links.
 */
async function updateSocialLinks() {
    // Create POST form
    let instagramField = document.getElementById("instagram");
    let linkedinField = document.getElementById("linkedin");
    let facebookField = document.getElementById("facebook");

    let data = new FormData();
    data.append("instagram", instagramField.value);
    data.append("linkedin", linkedinField.value);
    data.append("facebook", facebookField.value);

    // Submit to front controller
    var response = await fetch(
        "index.php?command=update_social_links",
        {
            method: "POST",
            body: data
        }
    );

    clearAlerts("social-alerts");
    if (!response.ok) {
        console.log("Response failed");
        createAlert("Failed to update social media links!", "danger", "social-alerts");
    } else {
        var obj = await response.json();  
        onSocialLinksUpdated(obj);
    }
}

/**
 * Notify the user that their social media links were updated.
 */
function onSocialLinksUpdated(data) {
    if (data.result === "success") {
        createAlert("Social media links updated!", "success", "social-alerts");
    } else {
        createAlert("Failed to update social media links!", "danger", "social-alerts");
    }
}