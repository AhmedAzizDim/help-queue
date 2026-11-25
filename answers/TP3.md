# TP3: statuses and relationships

## Exercise 1 - Question 1

Replacing the hidden `status` value with `sleeping` and submitting the form gives:

> The selected status is invalid.

The `Rule::in(Ticket::STATUSES)` validation rule accepts only `waiting`, `helping`,
or `done`. Validation fails before `save()`, so the ticket keeps its previous
status and Laravel redirects back with the error. The `@error('status')` block
on the detail page displays it.

With normal submissions, **Start helping** changes `waiting` to `helping` and
the button becomes **Mark as done**. The next submission changes the status to
`done`, and the update button disappears. Each valid update displays
**Status updated.**

`status` stays out of `$fillable`: the update method assigns this one validated
field directly and then calls `save()`.

## Exercise 2 - Question 2

In Tinker, running `Ticket::waiting()->toSql()` produces:

```sql
select * from "tickets" where "status" = ? order by "id" asc
```

The `?` is a parameter placeholder. `Ticket::waiting()->getBindings()` returns
`['waiting']`, so the query selects only tickets whose status is `waiting` and
sorts them by ascending ID. `toSql()` shows the SQL without executing it;
`Ticket::waiting()->get()` executes the query and retrieves those tickets.

The `waiting()` scope reuses the filter and ordering defined in `scopeWaiting()`.
The `helping()` scope similarly filters tickets whose status is `helping`.

For step 2.5, Hudson was changed from `waiting` to `helping` through the form.
The queue then showed Hudson under **Being helped**, **Waiting (7)**, and
**1 solved so far.** Litzy, completed in Exercise 1, no longer appeared as a card.

## Exercise 3 - Question 3

I created a ticket through the form using the name **Ahmed Aziz Dimassi** and
the topic **Laravel**. Its detail page showed **7 people ahead of you.**

I checked the Waiting section: it contained eight tickets, with my ticket in
position 8. The seven tickets before it were Dewayne, Lizzie, Alvina, Sven,
Tess, Odell, and AZ. Hudson was being helped and Litzy was done, so neither
was counted as waiting ahead of me.

The controller calculates the number with:

```php
$ahead = Ticket::waiting()->where('id', '<', $ticket->id)->count();
```

Only waiting tickets with a smaller ID are counted. IDs determine queue order
even when several tickets share the same creation timestamp. The message is
shown only while the current ticket is waiting and uses `person` for 1 or
`people` otherwise. The count above records the queue at the time of this check.

## Exercise 4 - Question 4

The topic badges now display JSON describing the related topic, including its
`id`, `name`, `created_at`, and `updated_at`, instead of only the topic name.
For example, the first badge contains `"id":1` and `"name":"Git"` along with
the topic's timestamps.

The views still print `{{ $ticket->topic }}`, but `topic` is now an Eloquent
relationship returning a `Topic` model, not a text column. When that model is
printed, Eloquent converts it to JSON and Blade escapes it for HTML display.
Exercise 5 will change the views to print `{{ $ticket->topic->name }}`.

After `php artisan migrate:fresh --seed`, the database contains five topics
and eight sample tickets. In Tinker, `Ticket::first()->topic->name` returned
`Git`, confirming the relationship works. The previous local database was
backed up before the rebuild.

At this checkpoint, the ticket-creation controller still uses the removed
`Ticket::TOPICS` constant. The creation form and validation are migrated to
`topic_id` in Exercise 5. Consequently, 35 tests pass and the existing
ticket-creation test fails at this intermediate checkpoint.

## Exercise 5 - Question 5

Submitting `topic_id=99` returns **The selected topic is invalid.** The
`exists:topics,id` rule produces this error because topic 99 does not exist.
The unmodified Laravel message would say **The selected topic id is invalid.**
The bonus third validation argument, `['topic_id' => 'topic']`, changes only
the displayed attribute name.

The form now uses database IDs, offers the five topics in alphabetical order,
preserves the selected topic after validation errors, and stores `topic_id`.
The badges display topic names. The queue eagerly loads each ticket's topic.
Since the migration permits a null topic, an unassigned ticket displays
`Unassigned` instead of causing an error.

Validation, old form values, escaped output, and the earlier exercises are
covered by the passing tests: 42 tests, 197 assertions at this checkpoint.

## Exercise 6 - Question 6

Clicking the **Git** badge opens `/topics/1`, which shows **2 tickets**:
Juana and Sallie. These are the observed seeded records in the local project;
random seeding can produce different counts in another installation.

Each topic page lists only that topic's tickets, ordered by ID, including
waiting, helping, and completed tickets. An unknown topic returns 404.

For the second bonus, the queue lists every topic and its total ticket count,
including topics with zero tickets. The controller uses
`Topic::withCount('tickets')->orderBy('name')->get()` and the view displays
`$topic->tickets_count`.

## Exercise 7 - Question 7

The live `/queue.json` response contains **8 items**, matching the eight
waiting tickets in the local database. The items are ordered by ticket ID.
Each nested `topic` object contains `id`, `name`, `created_at`, and `updated_at`.
A Python script, a mobile application, or a command-line client could consume
this JSON to display the queue.

The home page redirects to `/tickets`. Automated tests verify that helping
and completed tickets are excluded, topics are included, and an empty waiting
queue returns `[]`. All 49 tests pass, with 255 assertions at this checkpoint.
