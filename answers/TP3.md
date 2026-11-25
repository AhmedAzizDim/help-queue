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
