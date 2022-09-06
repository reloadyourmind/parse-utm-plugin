document.addEventListener("DOMContentLoaded", () => {
    if(document.cookie.indexOf('utm_tack_id=') > -1) {
    	console.info("User IP: " + userData.ip + " " + "User OS: " + userData.os);
    }
});