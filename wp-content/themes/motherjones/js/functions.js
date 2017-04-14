/**
 * Theme functions file.
 *
 * Contains handlers for navigation and widget area.
 */

function MJ_check_email (form) {
  form.signup_url.value = document.URL;

  var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
  var address = form.cons_email.value;
	var input_check = document.getElementById("email_field").value;

  if(reg.test(address) !== false) {
    if (input_check === "") {
      return true;
    }
    else {
			return false;
		}
  } else {
    alert("Please enter a valid email address.");
    form.cons_email.focus();
    return false;
  }
}
