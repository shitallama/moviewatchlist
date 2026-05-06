(() => {
	const init = () => {
		const message = document.querySelector('[data-auto-dismiss]');
		if (message) {
			setTimeout(() => {
				message.classList.add('is-dismissed');
			}, 5000);
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();