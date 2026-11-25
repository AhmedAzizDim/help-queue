# Help Queue

A Laravel application for practical sessions. Students choose a topic and join
the queue; the teacher moves tickets from waiting to helping to done.

## Requirements

- Git, Composer 2, PHP 8.5, and Node.js 24 with npm available in your terminal.
- PHP's SQLite extensions (`pdo_sqlite` and `sqlite3`) enabled. Composer checks
  the remaining PHP extension requirements during installation.

These are the versions used to verify this project. The committed lock files
keep the PHP and JavaScript dependency versions consistent.

## Installation

Run these commands in order from the directory where you keep your projects:

```sh
git clone https://github.com/AhmedAzizDim/help-queue.git help-queue
cd help-queue
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
composer run dev
```

The database file is not stored in Git. When `migrate` offers to create the
SQLite database, answer **yes**. The default `.env.example` already selects
SQLite. Seeding creates five topics and eight sample tickets.

`cp` also works in PowerShell as an alias for `Copy-Item`. In Windows Command
Prompt, use `copy .env.example .env` for that step.

Open the URL printed by the server, normally [http://localhost:8000](http://localhost:8000).
Keep the terminal running; press `Ctrl+C` when finished. If another application
uses port 8000, Laravel may choose the next available port.

## Using the application

- `/` redirects to `/tickets`, which separates helping and waiting tickets and
  displays the number solved.
- **Ask for help** creates a waiting ticket with a database-backed topic.
- **Details** shows queue position and the status and removal controls.
- Topic links open `/topics/{topic}` and list that topic's tickets. Topic totals
  include tickets in every status.
- `/queue.json` returns waiting tickets ordered by ID, including each topic.

## Checks

```sh
php artisan test
npm run build
```

Tests use a separate in-memory SQLite database. Exercise answers and the
Exercise 8 installation report are in [answers/TP3.md](answers/TP3.md).
