/**
 * Checks if the email is valid.
 * @param {string} email
 * @returns {boolean}
 */
export function checkEmail(email) {
	const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
	if (!emailRegex.test(email)) {
		alert('Invalid email');
		return false;
	}

	return true;
}

/**
 * Checks if the password is valid, a password must contains :
 * - At least 8 characters
 * - At least 1 uppercase letter
 * - At least 1 lowercase letter
 * - At least 1 number
 * - At least 1 special character
 *
 * Password length is already checked by the browser, so we don't need to check it here.
 * @param {string} password
 * @returns {boolean}
 */
export function checkPassword(password) {
	if (!password.match(/[A-Z]/)) {
		alert('Password must contain at least 1 uppercase letter');
		return false;
	}

	if (!password.match(/[a-z]/)) {
		alert('Password must contain at least 1 lowercase letter');
		return false;
	}

	if (!password.match(/[0-9]/)) {
		alert('Password must contain at least 1 number');
		return false;
	}

	if (!password.match(/[?!@#$%^&*\-_;,:=+\\|()\[\]{}~"'£¤°¨`<>§./]/)) {
		alert('Password must contain at least 1 special character');
		return false;
	}

	return true;
}
