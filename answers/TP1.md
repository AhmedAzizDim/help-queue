# TP1 — Exit questions

## 1. Which files does a request to `/tickets` go through?

`public/index.php` boots Laravel through `bootstrap/app.php`. Laravel matches the GET route in `routes/web.php`, which calls `index()` in `app/Http/Controllers/TicketController.php`. The controller uses `app/Models/Ticket.php` to query the SQLite `tickets` table in arrival order. It passes the results to `resources/views/tickets/index.blade.php`, which extends `resources/views/layouts/app.blade.php`. Laravel renders the HTML and sends the response to the browser.

The tickets migration creates the table beforehand; it does not run on each request.

## 2. What does a migration do, and why use it instead of creating a table by hand?

A migration describes a database schema change in PHP. `php artisan migrate` runs its `up()` method, and a rollback runs its `down()` method. The tickets migration creates the ID, name, topic, optional description, status (defaulting to `waiting`), and timestamps.

Migrations are version-controlled and repeatable, so teammates and new installations can reproduce the same schema without manually editing their databases. Laravel records which migrations have already run.

## 3. Why did `Ticket::create(...)` fail before `$fillable` was added?

`create()` uses mass assignment to set several attributes at once. Eloquent protects models from unexpected input by refusing to mass-assign fields that have not been allowed. Before `$fillable` was defined, `name` and `topic` were not permitted, so Laravel raised a `MassAssignmentException`.

The Ticket model permits only `name`, `topic`, and `description`. Fields such as `status` remain protected from mass assignment, and the database supplies the default status `waiting`.
