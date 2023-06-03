import {randomSalt, sha512} from "./utils.js";

document.addEventListener('DOMContentLoaded', () => {
	/**
	 * @type {HTMLFormElement}
	 */
	const form = document.querySelector('#register-form');

	form.addEventListener('submit', async e => {
		e.preventDefault();
		const password = form.querySelector('#password').value;
		const confirmPassword = form.querySelector('#confirm-password').value;

		if (password !== confirmPassword) {
			alert('Passwords do not match');
			return;
		}

		const salt = await randomSalt();
		const hashedPassword = await sha512(salt + password);
		const hashedConfirmPassword = await sha512(salt + confirmPassword);

		const formData = new FormData(form);
		formData.set('password', hashedPassword);
		formData.set('confirm-password', hashedConfirmPassword);
		formData.set('salt', salt);

		const response = await fetch('/register', {
			method: 'POST',
			body: formData
		});

		if (response.ok) {
			console.log(response);
			// window.location.href = '/login';
		} else {
			alert(await response.text());
		}
	});
});
