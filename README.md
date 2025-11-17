## Demo Project

This is a Laravel 12 demo project for testing Excel export and data dashboard.

## Requirements
- PHP 8.2+
- Composer
- MySQL 8+
- XAMPP (optional)

## Installation
1. Clone the repository: git clone https://github.com/xinsheng519/demo3.git
2. Copy env.example file replace your database and change name to .env
3. Run php artisan key:generate
3. Run php artisan config:cache
4. Run composer install
5. Run php artisan migrate
6. Run php artisan db:seed
7. Run php artisan serve

- if composer install got issue try to going php.ini find out the extension=gd uncomment it

## Notes
1. I decided not to implement queue processing for the Excel export feature.
- Faster development – I needed to complete the module quickly, so using a direct export method allows faster implementation.
- Easy for you to use – Without queues, you can run the export immediately without setting up extra services like Supervisor, queue work
- The export size is small enough – Current data volume is manageable, so direct export will not cause performance issues.

2. I chose to put all of the logic directly inside the controller instead of creating separate Service classes.
- Faster development – This module needed to be delivered quickly, so writing the logic directly in the controller helped speed up development.
- Easier for you to review and test – All relevant logic is kept in one place, making it simpler for the team to understand and test without navigating multiple files.