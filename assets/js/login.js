const message = document.querySelector('[data-auto-dismiss]');
		if (message) {
			setTimeout(() => {
				message.classList.add('is-dismissed');
			}, 5000);
		}

	const toggles = document.querySelectorAll('.toggle-password');
	toggles.forEach((toggle) => {
		toggle.addEventListener('click', () => {
			const targetId = toggle.getAttribute('data-target');
			const input = document.getElementById(targetId);
			if (!input) {
				return;
			}
			const isPassword = input.type === 'password';
			input.type = isPassword ? 'text' : 'password';
			toggle.textContent = isPassword ? 'Hide' : 'Show';
	});
});