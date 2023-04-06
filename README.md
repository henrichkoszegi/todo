# Todo App

## Tech Stack

- Laravel
- Inertia
- Vue
- TailwindCSS
- Vite

## Installation

- Make sure you have [Docker Desktop](https://www.docker.com/products/docker-desktop) installed.
- Open your terminal and run the following commands.
- `git clone https://github.com/henrichkoszegi/todo.git`
- `cd todo`
- `cp .env.sail .env`
- `./vendor/bin/sail up`
- `./vendor/bin/sail composer install`
- `./vendor/bin/sail php artisan migrate:fresh --seed`
- `./vendor/bin/sail npm install`
- `./vendor/bin/sail npm run dev`
- Visit [http://localhost/](http://localhost/) in your browser.

## License

&copy; 2023 All rights reserved.
