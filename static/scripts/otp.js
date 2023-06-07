async function fetchAndReplaceOTP(otp) {
	const response = await fetch('/get-otp', {
		method: 'GET',
	});
	const data = await response.text();
	if (data !== otp.textContent) {
		otp.textContent = data;
	}
}

document.addEventListener('DOMContentLoaded', () => {
	const otp = document.querySelector('#otp');
	otp.addEventListener('click', async () => await navigator.clipboard.writeText(otp.textContent.trim()));

	setInterval(async () => await fetchAndReplaceOTP(otp), 20 * 1000);

	window.addEventListener('focus', async () => await fetchAndReplaceOTP(otp));
});
