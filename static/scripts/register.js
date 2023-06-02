import {randomSalt, sha512} from "./utils.js";

document.addEventListener('DOMContentLoaded', () => {
	/**
	 * @type {HTMLFormElement}
	 */
	const form = document.querySelector('#register-form');

	form.addEventListener('submit', async e => {
		const password = form.querySelector('#password').value;
		const confirmPassword = form.querySelector('#confirm-password').value;

		if (password !== confirmPassword) {
			e.preventDefault();
			alert('Passwords do not match');
		}

		const salt = await randomSalt();
		const hashedPassword = await sha512(salt + password);
		const hashedConfirmPassword = await sha512(salt + confirmPassword);

		const formData = new FormData(form);
		formData.set('password', hashedPassword);
		formData.set('confirm-password', hashedConfirmPassword);

		const response = await fetch('/register', {
			method: 'POST',
			body: formData
		});

		if (response.status === 200) {
			console.log('Registered successfully');
			window.location.href = '/login';
			history.replaceState(null, null, '/login');
		}
	});
});