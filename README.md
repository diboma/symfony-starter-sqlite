# SQLITE Symfony Starter

## Requirements

Make sure you have the following installed:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [DDEV](https://ddev.com/)
- [Node.js](https://nodejs.org/en/)

## Get started

### Initialize the project

- Create an `.env.local` file. Add `APP_ENV` and `MAILER_DSN` to it.
- Make sure Docker Desktop is running.
- Spin up the project with `ddev start`.
- Install the dependencies by running `ddev composer install`.
- Finally run `npm install`.
- You can run  `ddev describe` to get the project details.

### Install the database

- Run the migrations: `ddev migrate`.  
  NOTE: this is a shortcut for `ddev php bin/console doctrine:migrations:migrate`

- If you get an error saying the database does not exist,  
  then run `ddev database -c` to create it.  
  NOTE: this is a shortcut for `ddev php bin/console doctrine:database:create`.

- Seed the database: `ddev seed`.  
  NOTE: this is a shortcut for `ddev php bin/console doctrine:fixtures:load`.

### Install AssetMapper and Sass files

- Install AssetMapper: `ddev importmap -i`.  
  NOTE: this is a shortcut for `ddev php bin/console importmap:install`.

- For **development** run Sass watch mode: `ddev sass -w`.  
  NOTE: this is a shortcut for `ddev php bin/console sass:build --watch`.

- For **production** you should run:
  ```bash
  ddev php bin/console sass:build
  ddev php bin/console asset-map:compile
  ```

### Launch the project

- Run `ddev launch` to open your project.  

- For **development** you can add **live reloads with DDEV Browsersync**:

  - Make sure you have the DDEV Browsersync add-on installed. If not, run:
    ```bash
    ddev add-on get ddev/ddev-browsersync
    ddev restart
    ```

  - Run `ddev browsersync` and you will have live reload on port `3000`.

## Note on running console commands

### Shortcut command

You need to run console commands from within the DDEV container.  
This means that you cannot use `symfony console [command]`.  
Instead you always have to run `ddev php bin/console [command]`.

However, a `console` shortcut has been added to the `.ddev/commands/web` folder, so instead of `ddev php bin/console [command]` you can run

**`ddev console [command]`**

For example:

```bash
ddev console debug:router
# is equal to
ddev php bin/console debug:router
```

### Custom commands

Also some custom commands have been provided in the `.ddev/commands/host` folder. Feel free to add your own.

| Command                            | Action                                             |
| :--------------------------------- | :------------------------------------------------- |
| `ddev clear-cache`                 | Clear the cache                                    |
| `ddev database -c / --create`      | Create the configured database                     |
| `ddev database -d / --drop`        | Drop the configured database                       |
| `ddev database -e / --export`      | Export the database to the .ddev/db_exports folder |
| `ddev database -i / --import`      | Import the database from the exporte sql-file      |
| `ddev database -m / --migrate`     | Run the migrations                                 |
| `ddev database -s / --seed`        | Load data fixtures to database                     |
| `ddev importmap -i / --install`    | Download all AssetMapper assets                    |
| `ddev importmap -u / --update`     | Update all AssetMapper assets                      |
| `ddev make -c / --controller`      | Create a new controller class                      |
| `ddev make -cr / --crud`           | Create CRUD for Doctrine entity class              |
| `ddev make -e / --entity`          | Create CRUD for Doctrine entity class              |
| `ddev make -fx / --fixtures`       | Create a new class to load Doctrine fixtures       |
| `ddev make -fo / --form`           | Create a new Form class                            |
| `ddev make -m / --migration`       | Create a new migrations based on database changes  |
| `ddev make -tc / --twig-component` | Create a twig (or live) component                  |
| `ddev make -te / --twig-extension` | Create a new Twig extension with its runtime class |
| `ddev make -u / --user`            | Create a new security user class                   |
| `ddev make -v / --validator`       | Create a new validator and constraint class        |
| `ddev migrate`                     | Run the migrations                                 |
| `ddev routes`                      | Display current routes                             |
| `ddev sass -b / --build`           | Build CSS files                                    |
| `ddev sass -w / --watch`           | Build CSS files and watch for changes              |
| `ddev seed`                        | Load data fixtures to database                     |
| `ddev sync`                        | Sync metadata storage                              |
