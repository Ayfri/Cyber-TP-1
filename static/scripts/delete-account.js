import {sha512} from "./hash.js";

document.addEventListener('DOMContentLoaded', () => {
	const deleteAccountForm = document.querySelector('#delete-account-form');
	deleteAccountForm.addEventListener('submit', async event => {
		event.preventDefault();
		if (!confirm('Are you sure you want to delete your account ?')) return;

		const password = deleteAccountForm.querySelector('#password').value;
		const hashedPassword = await sha512(password);

		const formData = new FormData(deleteAccountForm);
		formData.set('password', hashedPassword);

		const response = await fetch('/delete-account', {
			method: 'POST',
			body: formData
		});

		if (response.ok) {
			window.location.href = '/otp-verify';
		} else {
			alert(`Error while deleting account : ${await response.text()}`);
		}
	});
});
