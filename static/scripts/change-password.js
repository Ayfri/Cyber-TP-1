import {sha512} from "./utils.js";

document.addEventListener('DOMContentLoaded', () => {
	/**
	 * @type {HTMLFormElement}
	 */
	const form = document.querySelector('#change-password-form');

	form.addEventListener('submit', async e => {
		e.preventDefault();
		const currentPassword = form.querySelector('#current-password').value;
		const newPassword = form.querySelector('#new-password').value;
		const confirmPassword = form.querySelector('#confirm-password').value;

		if (newPassword !== confirmPassword) {
			alert('Passwords do not match');
			return;
		}

		if (currentPassword === newPassword) {
			alert('New password cannot be same as current password');
			return;
		}

		const hashedCurrentPassword = await sha512(currentPassword);
		const hashedNewPassword = await sha512(newPassword);
		const hashedConfirmPassword = await sha512(confirmPassword);

		const formData = new FormData(form);
		formData.set('current-password', hashedCurrentPassword);
		formData.set('new-password', hashedNewPassword);
		formData.set('confirm-password', hashedConfirmPassword);

		const response = await fetch('/change-password', {
			method: 'POST',
			body: formData
		});

		if (response.ok) {
			window.location.href = '/otp-verify';
		} else {
			alert(await response.text());
		}
	});
});
