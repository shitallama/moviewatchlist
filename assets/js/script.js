(() => {
	const init = () => {
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

		// Password visibility toggle
		if (!window.__passwordToggleBound) {
			window.__passwordToggleBound = true;
			document.addEventListener('click', (e) => {
				const button = e.target.closest('.toggle-password');
				if (!button) {
					return;
				}
				e.preventDefault();
				const targetId = button.getAttribute('data-target');
				const input = document.getElementById(targetId);
				const icon = button.querySelector('img.icon');

				if (input && icon) {
					const isPassword = input.type === 'password';
					input.type = isPassword ? 'text' : 'password';
					icon.src = isPassword ? icon.getAttribute('data-icon-open') : icon.getAttribute('data-icon-closed');
					button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
				}
			});
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
