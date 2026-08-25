import { Controller } from '@hotwired/stimulus';

const STORAGE_KEY = 'theme';
const DARK = 'dark';
const LIGHT = 'light';

/*
 * Switches the site between the light and the dark palette.
 *
 * The `dark` class lives on <html> — both the token overrides in `app.css` and
 * Tailwind's `dark` variant key off it — and is applied before first paint by
 * the inline script in `app/base.html.twig`. That script and this controller
 * share the storage key and the values it holds, so they must stay in sync.
 *
 * The button renders its icon and label for both themes and lets CSS pick the
 * matching one, so nothing here touches the DOM beyond that single class.
 */
export default class extends Controller {
  connect() {
    this.systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
    this.systemThemeChanged = () => {
      // An explicit choice outranks the system one, and there is no UI to clear it
      if (null === readTheme()) {
        applyTheme(this.systemTheme.matches ? DARK : LIGHT);
      }
    };

    this.systemTheme.addEventListener('change', this.systemThemeChanged);
  }

  disconnect() {
    this.systemTheme.removeEventListener('change', this.systemThemeChanged);
  }

  toggle() {
    const theme = document.documentElement.classList.contains(DARK) ? LIGHT : DARK;

    applyTheme(theme);
    storeTheme(theme);
  }
}

function applyTheme(theme) {
  document.documentElement.classList.toggle(DARK, DARK === theme);
}

function readTheme() {
  try {
    return localStorage.getItem(STORAGE_KEY);
  } catch {
    // Storage access throws rather than degrades — Safari in private browsing
    return null;
  }
}

function storeTheme(theme) {
  try {
    localStorage.setItem(STORAGE_KEY, theme);
  } catch {
    // See readTheme()
  }
}
