# Help Queue — Laravel TP1

A Laravel 13 application implementing the requirements in **Laravel-TP1-EN.pdf**, including the bonus ticket detail page.

## Requirements

- PHP **8.4.1 or newer** with SQLite/PDO, mbstring, XML/DOM, cURL, fileinfo, iconv, tokenizer, and ZIP support. The application supports PHP 8.3, but the locked Pest/PHPUnit development dependencies require PHP 8.4.1+.
- Composer 2
- Node.js 22.12+ (or 20.19+) and npm

## Install and run

From the repository root:

```sh
composer install
cp .env.example .env
php artisan key:generate
```

Copy `.env.example` only on a fresh installation; keep an existing `.env` and application key. SQLite is the default database. Create the local database file if it is missing:

```sh
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate
php artisan db:seed --class=TicketSeeder
npm ci --ignore-scripts
npm run build
composer run dev
```

`TicketSeeder` adds Lina, Omar, and Sara from the assignment. Repeating this seed command does not duplicate unchanged sample tickets. SQLite and `.env` are ignored by Git; seed the sample tickets when installing a fresh checkout. The default `DatabaseSeeder` remains Laravel's example user seeder, so use `--class=TicketSeeder` for the TP1 data.

In the prepared Codex cloud workspace, first run `export PATH=/workspace/tooling/bin:$PATH` to activate the installed PHP and Composer commands.

## Pages and expected behavior

| Path | Behavior |
| --- | --- |
| `/` | Laravel welcome page |
| `/hello` | `Hello from Laravel!` |
| `/hello/Souhail` | Blade heading `Hello Souhail!`; works with any single name segment |
| `/helo` or `/hello/a/b` | 404 |
| `/tickets` | Database-backed queue, numbered in arrival order; `3 waiting` after sample seeding |
| `/tickets/{id}` | Name, topic, description, and relative creation time |
| `/tickets/999` | 404 if no ticket with that ID exists |

The queue uses the TP1 query to display all tickets ordered by `created_at`, with ID as a deterministic tie-breaker. The initial status is `waiting`. Ticket name, topic, and description are fillable; status is not. Blade escapes user-supplied fields in the hello page, list, and detail page. An empty queue displays `Nobody is waiting.`

## Validation

```sh
php artisan db:table tickets
php artisan test --compact
npm run build
```

The tickets migration defines seven columns: `id`, `name`, `topic`, `description`, `status`, `created_at`, and `updated_at`. Feature tests use an isolated in-memory SQLite database and do not erase your local tickets.

The exit-question answers are in [answers/TP1.md](answers/TP1.md). This checkpoint history was reconstructed from the completed application, rather than recorded during the original practical sessions. The bonus ticket page is included in CP8.

## TP1 checkpoints

GitHub displays the latest commit affecting each file or folder. A folder containing files from several checkpoints shows only the most recent one. All eight checkpoints are available in the [commit history](https://github.com/AhmedAzizDim/help-queue/commits/main/).

| Checkpoint | Files and functionality |
| --- | --- |
| TP1-CP1 | Fresh Laravel application and starter configuration |
| TP1-CP2 | `routes/web.php`: `/hello` returns the required sentence |
| TP1-CP3 | `routes/web.php`: `/hello/{name}` accepts a name |
| TP1-CP4 | `resources/views/hello.blade.php`: escaped greeting view |
| TP1-CP5 | Shared layout, ticket controller, and initial queue view |
| TP1-CP6 | Tickets migration and initial Ticket model |
| TP1-CP7 | Ticket fillable fields and repeatable sample seeder |
| TP1-CP8 | Database-backed controller and queue, bonus detail page, routes, and tests |

Files modified again in later exercises display that later checkpoint. For example, `routes/web.php` displays TP1-CP8 because it also contains the final ticket routes.
