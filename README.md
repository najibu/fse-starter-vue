![Signable](https://assets-cdn.signable.co.uk/images/signable.blue.png)

# Laravel Starter Kit

You can choose any frameworks that you want, however please be prepared to explain your decisions. Also please bear in
mind the frameworks and languages that we use as a company - Laravel, PHP, Vue, React and Tailwind.

We have prepared three different starter kits, all based on the latest Laravel version but with a different flavour of
front end depending on your existing skill sets.

- Livewire: https://github.com/Signable/fse-starter-livewire
- Vue: https://github.com/Signable/fse-starter-vue
- React: https://github.com/Signable/fse-starter-react

## Project details

All expectations and requirements for your project will have been included in your 2nd Stage interview request. Please
consult these fully for more information on how and when to hand the project in.

## Getting started

We are looking to understand your design, code and structure decisions rather than see a 100% polished project, so get
and running in the simplest way possible.
This could be using SQLite and `php artisan serve`, or a service like Herd or Valet using MySQL or PostgreSQL.

### Assumptions

- PHP v8.3+

### Starting

- Clone or fork your chosen starter kit
- Copy `.env.example` to `.env` and edit with your own requirements
- `composer install`
- `php artisan key:generate --ansi`
- `php artisan migrate`
- `yarn && yarn build`
- `php artisan test` or `php artisan test -p` for parallel testing
- Work your magic!

#### Notes

We've included several useful packages and configurations for Laravel including Debugbar, IDE Helper Generator, Pint,
Rector and ParaTest.
Running `composer update` will automatically run some of those packages.

## Running locally

1. composer install && yarn && yarn build
2. cp .env.example .env && php artisan key:generate
3. Configure DB in .env and run `php artisan migrate`
4. Run tests: `php artisan test`
5. Dev server: `yarn dev` and `php artisan serve`

## Notes

- Display name is stored in localStorage key: `chat_user_name`
- Messages are anonymous in DB (fields: id, user_name, content, timestamps)
- Server sanitizes `content` before returning in API to mitigate XSS

## CI & formatting

- php-cs-fixer configured; run `composer run cs:fix` locally.
- CI runs php-cs-fixer (dry-run) + phpunit

## AI usage

This project used AI assistance for refactors. (Brief description of what AI helped with.)
