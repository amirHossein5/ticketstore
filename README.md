Landing page:
![Screenshot 2024-02-17 at 18-54-58 ticketstore](https://github.com/amirHossein5/ticketstore/assets/68776630/7c8deab3-5e72-48fa-a09c-1ffa915824c8)

Purchase ticket:
![Screenshot 2024-02-17 at 18-55-58 payment](https://github.com/amirHossein5/ticketstore/assets/68776630/cbf1d420-a71b-4f80-87c1-af09a06051ed)

Viewing purchased tickets:
![Screenshot 2024-02-17 at 18-56-09 your order](https://github.com/amirHossein5/ticketstore/assets/68776630/d199016b-d9fe-47a8-977f-93ac4f07df48)

Dashboard:
![Screenshot 2024-02-17 at 18-54-41 dashboard](https://github.com/amirHossein5/ticketstore/assets/68776630/7ce15e46-d4d4-417e-8cc7-8b3c35c3f913)

Viewing ticket orders:
![Screenshot 2024-02-17 at 18-56-46 The T20 South Africa vs Australia Orders](https://github.com/amirHossein5/ticketstore/assets/68776630/b68e6411-7b75-424d-b70e-2e28a90e7697)


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
- User, orders selected published ticket with specified quantity,
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
