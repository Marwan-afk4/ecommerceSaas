const storageKey = 'softora-theme';

function currentTheme() {
    return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
}

function applyTheme(theme, persist = true) {
    const dark = theme === 'dark';

    document.documentElement.classList.toggle('dark', dark);
    document.documentElement.style.colorScheme = theme;

    if (persist) {
        localStorage.setItem(storageKey, theme);
    }

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', dark ? 'true' : 'false');
        button.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
    });
}

function toggleTheme() {
    applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
}

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-theme-toggle]')) {
        toggleTheme();
    }
});

document.addEventListener('livewire:navigated', () => {
    applyTheme(currentTheme(), false);
});

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
    if (localStorage.getItem(storageKey) === null) {
        applyTheme(event.matches ? 'dark' : 'light', false);
    }
});

applyTheme(currentTheme(), false);
