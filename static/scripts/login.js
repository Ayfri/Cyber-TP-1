import {sha512} from "./hash.js";

document.addEventListener('DOMContentLoaded', () => {
	/**
	 * @type {HTMLFormElement}
	 */
	const form = document.querySelector('#login-form');

	form.addEventListener('submit', async e => {
		e.preventDefault();
		const password = form.querySelector('#password').value;
		const hashedPassword = await sha512(password);

		const formData = new FormData(form);
		formData.set('password', hashedPassword);

		const response = await fetch('/login', {
			method: 'POST',
			body: formData
		});

		if (response.ok) {
			window.location.href = '/';
		} else {
			alert(await response.text());
		}
	});
});
