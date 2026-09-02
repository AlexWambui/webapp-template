
# Notes

## Setting up modules

Create the `modules` folder.

Add the modules folder to `composer.json` file so that it's autoloaded.

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/",
        "Modules\\": "modules/"
    }
},
```

Dump the autoload files so that it will from now be autoloaded.

```bash
composer dump-autoload
```

## Artisan Commands for Module Files

Create a migration in the modules folder:

```bash
php artisan make:migration create_<table_name_plural>_table --path=modules/<ModuleName>/Database/Migrations --create=<table_name_plural>
```

Create a migration for adding a column or adjusting an existing table:

```bash
php artisan make:migration add_columns_to_<table_name_plural>_table --path=modules/<ModuleName>/Database/Migrations --table=<table_name_plural>
```
