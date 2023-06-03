document.addEventListener('DOMContentLoaded', () => {
	const disconnectButton = document.querySelector('#disconnect');

	disconnectButton.addEventListener('click', async () => {
		const response = await fetch('/logout');
		if (response.ok) {
			window.location.href = '/login';
		} else {
			alert(`Error while logging out : ${await response.text()}`);
		}
	});
});
