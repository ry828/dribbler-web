

function validatePassword() {
	
    var password = document.getElementById("password");
    var confirm_password = document.getElementById("confirm_password");
    var ok = true;
	
    if (password.value != confirm_password.value) {
        confirm_password.setCustomValidity("Passwords Don't Match");
        ok = false;
    } else {
        confirm_password.setCustomValidity('');
    }
	
    return ok;
}