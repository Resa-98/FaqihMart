# AGENTS.md

## Project overview

CodeIgniter **3** (bcit-ci framework) PHP application, run locally via **XAMPP** (Apache + mod_rewrite). The repo root is the web root; `index.php` is the front controller.

- `system/` — vendored CI3 framework. **Never modify**; upgrades overwrite it.
- `application/` — all app code: `controllers/`, `models/`, `views/`, `config/`, `core/` (for `MY_Controller` etc.), `helpers/`, `libraries/`, `hooks/`.
- The app is essentially a fresh skeleton: only the default `Welcome` controller exists. No custom models, core classes, or autoloaded libraries/helpers yet.

## Running the app

- Serve via Apache (XAMPP), e.g. `http://localhost/Faqihmart/`. Clean URLs depend on the root `.htaccess` rewrite (`index.php/$1`); `index_page` is `''`. A bare `php -S` dev server will not route correctly without an equivalent router script.
- Environment is `$_SERVER['CI_ENV']`, defaulting to `development` (see `index.php`).

## Gotchas (verify before relying on these)

- `application/config/config.php` has `base_url = 'http://localhost/BaseFullstackProject/'` — a stale value from a template project. It does not match this directory; fix/confirm it when adding `base_url()`-dependent URLs, assets, or redirects.
- Database is **not configured**: `application/config/database.php` uses `mysqli` with empty `username`, `password`, and `database`. Fill in credentials before any DB work; nothing is autoloaded (`$autoload['libraries']` is empty — load `database` per-controller or add to autoload).
- The root `.htaccess` denies direct web access to `.md`, `.json`, `.sql`, `.log`, and dotfiles. Do not expect JSON/markdown assets in the web root to be fetchable over HTTP.
- No test suite, no CI workflows, no linter/formatter config, and no `tests/` directory exist. The `composer.json` scripts (`test:coverage` referencing `tests/travis/...`) are CI3 framework leftovers and are not usable here. There is no project-level verification command — test changes through the browser.

## Conventions

- New app classes follow CI3 naming: controllers in `application/controllers/` named `Ucfirst.php`, models named `*_model.php` in `application/models/`, views in `application/views/` (often grouped by controller name).
- `.gitignore` ignores `vendor/`, `composer.lock`, and everything in `application/cache/` and `application/logs/` except their `index.html` stubs — don't commit generated cache/log files.
