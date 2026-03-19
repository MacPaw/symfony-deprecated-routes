# AGENTS.md

## Cursor Cloud specific instructions

This is a **PHP Symfony Bundle library** (`macpaw/symfony-deprecated-routes`), not a standalone application. There is no server to start or database to connect to.

### Prerequisites

- PHP >= 8.1 (8.3 recommended) with extensions: mbstring, xml, curl, zip, intl, dom
- Composer

### Key commands

| Task | Command |
|---|---|
| Install dependencies | `composer install` |
| Run tests | `vendor/bin/phpunit` |
| Lint (code style) | `vendor/bin/phpcs` |
| Static analysis | `vendor/bin/phpstan analyse` |
| Validate composer.json | `composer validate` |

### Notes

- No `composer.lock` is committed — `composer install` resolves latest compatible versions each time.
- The `validate` script in `composer.json` shadows the `composer validate` command; Composer warns about this but it's harmless.
- PHPStan runs at `level: max` — see `phpstan.neon` for excluded paths.
- phpcs uses PSR-12 plus custom rules — see `phpcs.xml.dist`.
- Tests include both unit (`tests/Unit/`) and functional (`tests/Functional/`) suites using a test kernel at `Macpaw\SymfonyDeprecatedRoutes\Tests\App`.
- Docker (`docker-compose.yaml`) is only for multi-PHP-version testing; not needed for standard development.
