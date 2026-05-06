(function () {
	const root = document.documentElement;
	const storageKey = 'moviewatchlist-theme';
	const toggle = document.querySelector('[data-theme-toggle]');
	const themeIcon = document.querySelector('[data-theme-icon]');
	const iconBase = themeIcon ? themeIcon.getAttribute('src').split('/').slice(0, -1).join('/') + '/' : 'assets/icons/';
	const sunIconPath = `${iconBase}sun.svg`;
	const moonIconPath = `${iconBase}moon.svg`;

	const applyTheme = (theme) => {
		root.setAttribute('data-theme', theme);
		if (!toggle) {
			return;
		}
		const isDark = theme === 'dark';
		toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
		if (themeIcon) {
			themeIcon.src = isDark ? sunIconPath : moonIconPath;
			themeIcon.alt = '';
		}
		const label = toggle.querySelector('span');
		if (label) {
			label.textContent = isDark ? 'Light' : 'Dark';
		}
	};

	const saved = localStorage.getItem(storageKey);
	const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
	const initialTheme = saved || (prefersDark ? 'dark' : 'light');
	applyTheme(initialTheme);

	if (toggle) {
		toggle.addEventListener('click', () => {
			const current = root.getAttribute('data-theme') || initialTheme;
			const next = current === 'dark' ? 'light' : 'dark';
			applyTheme(next);
			localStorage.setItem(storageKey, next);
		});
	}
})();
