# SQLITE Symfony Starter

## Requirements

Make sure you have the following installed:

- [Symonfy CLI](https://symfony.com/download)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/en/)

## Get started

### Initialize the project

- Create an `.env.local` file. Add `APP_ENV` and `MAILER_DSN` to it.
- Run `composer install`.
- Run `npm install`.

### Install the database

- Create the database: `symfony console doctrine:database:create`.
- Run the migrations: `symfony console doctrine:migrations:migrate`
- Seed the database: `symfony console doctrine:fixtures:load`.

### Install AssetMapper and Sass files

- Install AssetMapper: `symfony console importmap:install`.

- For **development** run Sass watch mode:  
  `symfony console sass:build --watch`.

- For **production** you should run:
  ```bash
  symfony console sass:build
  symfony console asset-map:compile
  ```

### Launch the project

Spin up the project with `symfony serve`.  
A web server will be listening at `http://127.0.0.1:8000`
