document.addEventListener('DOMContentLoaded', () => {
	const otp = document.querySelector('#otp');

	otp.addEventListener('click', async () => await navigator.clipboard.writeText(otp.textContent.trim()));

	setInterval(async () => {
		const response = await fetch('/get-otp', {
			method: 'GET',
		});
		const data = await response.text();
		if (data !== otp.textContent) {
			otp.textContent = data;
		}
	}, 20 * 1000);
});
