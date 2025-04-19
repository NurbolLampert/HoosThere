/**
 * Add an alert message to the page.
 */
function createAlert(message, type) {
    let alert = document.createElement("p");
    alert.classList.add(
        "alert", `alert-${type}`
    );
    alert.innerHTML = message;
    document.getElementById("alerts").appendChild(alert);
}

/**
 * Clear all alert messages from the page.
 */
function clearAlerts() {
    document.getElementById("alerts").innerHTML = "";
}