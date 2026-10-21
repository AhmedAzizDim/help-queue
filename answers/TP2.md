Question 1

After running:

```bash
php artisan migrate:fresh --seed

returns a 404 Not Found response.

Question 3: Laravel checks routes from top to bottom. /tickets/{ticket} matches first, with {ticket} = "create". Laravel then looks for a ticket with id “create”, finds none, and returns 404.

Question 5: the name you typed is still there. The value="{{ old('name') }}" attribute puts it back.

Question 6: anyone can change the HTML or send a request without the form at all. Only the server’s Rule::in(Ticket::TOPICS) can be trusted.
