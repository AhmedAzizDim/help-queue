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
