document.addEventListener('DOMContentLoaded', () => {
	const otpForm = document.querySelector('#otp-verify-form');
	/**
	 * @type {HTMLInputElement}
	 */
	const otpInput = document.querySelector('#otp');

	const otpCancelButton = document.querySelector('#cancel');

	otpInput.addEventListener('beforeinput', (e) => {
		if (otpInput.value.length > otpInput.maxLength - 1 && e.inputType.match(/insert/i)) {
			e.preventDefault();
		}
	});

	otpForm.addEventListener('submit', async (e) => {
		e.preventDefault();
		const formData = new FormData(otpForm);

		const response = await fetch('/otp-verify', {
			method: 'POST',
			body: formData
		});

		if (response.ok) {
			window.location.href = '/';
		} else {
			alert(await response.text());
		}
	});

	otpCancelButton.addEventListener('click', async (e) => {
		e.preventDefault();
		const data = new FormData();
		data.set('cancelled', 'true');
		const response = await fetch('/otp-verify', {
			method: 'POST',
			body: data
		});

		if (response.ok) {
			window.location.href = '/';
		} else {
			alert(await response.text());
		}
	});
});
