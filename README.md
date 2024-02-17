// images

- Adding promoter via `php artisan add-promoter {email}`.
- Promoter creates account via emailed temporary link, then verifies his email.
- Promoter creates tickets from `/dashboard/tickets/create`.
- Ticket images are optimized using intervention library.
- From `/dashboard` promoter can
    - view
    - edit
    - publish
    - see most recent orders of a ticket
    - send message to attendees
- User orders selected published ticket with specified quantity,
and will be redirected to `/orders/order-code` temporary url,
to see order information and purchased ticket codes.

## Installation

```php
git clone https://github.com/amirhossein5/ticketstore
cd ticketstore
composer install
npm install && npm run build
cp .env.example .env
php artisan migrate:fresh --seed
```

Seeded promoter email: `promoter@gmail.com`, password: `password`
