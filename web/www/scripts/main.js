/**
 * Add an alert message to the page.
 */
function createAlert(message, type, id = "alerts") {
    let alert = document.createElement("p");
    alert.classList.add(
        "alert", `alert-${type}`
    );
    alert.innerHTML = message;
    document.getElementById(id).appendChild(alert);
}

/**
 * Clear all alert messages from the page.
 */
function clearAlerts(id = "alerts") {
    document.getElementById(id).innerHTML = "";
}