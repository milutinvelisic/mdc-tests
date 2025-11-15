# Laravel Project Setup

Requirements: PHP ^8.2

Setup Instructions:

1. Create a .env file and copy contents from .env.example
   cp .env.example .env

2. Update DB_USERNAME and DB_PASSWORD in .env according to your local setup

3. Create the database:
   laravel_test;

4. Install composer dependencies:
   composer install

5. Generate application key:
   php artisan key:generate

6. Run migrations and seed the database:
   php artisan migrate:fresh --seed

7. Start the queue worker:
   php artisan queue:work

Test User Credentials:
Email: test@example.com
Password: @123qweR
