# Laravel DB Craft

[![Latest Version on Packagist](https://img.shields.io/packagist/v/robinncode/lara-db-craft.svg?style=flat-square)](https://packagist.org/packages/robinncode/lara-db-craft)
[![Total Downloads](https://img.shields.io/packagist/dt/robinncode/lara-db-craft.svg?style=flat-square)](https://packagist.org/packages/robinncode/lara-db-craft)

Laravel DB Craft is a powerful package that automatically generates migration and seeder files from your existing database connections. Perfect for reverse engineering databases, creating backups, or migrating from legacy systems.

## Features

- ✨ **Automatic Migration Generation**: Generate Laravel migration files from existing database tables
- 🔄 **Seeder Generation**: Create seeders with actual table data
- 🎯 **Table-Specific Generation**: Generate for all tables or specific ones
- 🗄️ **Multi-Database Support**: Works with MySQL, PostgreSQL, and SQLite
- 🔗 **Foreign Key Detection**: Automatically detects and includes foreign key constraints
- 📊 **Index Support**: Preserves indexes and unique constraints
- ⚙️ **Configurable**: Exclude tables, customize paths, and more
- 🚀 **Easy to Use**: Simple artisan commands

## Requirements

- PHP 8.0 or higher
- Laravel 9.x, 10.x, or 11.x
- Database connection (MySQL, PostgreSQL, or SQLite)

## Installation

You can install the package via composer:

```bash
composer require robinncode/laravel-db-craft
```

The package will automatically register its service provider.

Optionally, publish the configuration file:

```bash
php artisan vendor:publish --provider="RobinNcode\LaravelDbCraft\DbCraftServiceProvider"
```

This will create a `config/db-craft.php` file where you can customize the package behavior.

## Usage

### Generate Migrations

#### Generate migrations for all tables:

```bash
php artisan get:migration
```

#### Generate migration for a specific table:

```bash
php artisan get:migration users
```

#### Use a specific database connection:

```bash
php artisan get:migration --connection=mysql
```

### Generate Seeders

#### Generate seeders for all tables with data:

```bash
php artisan get:seeder
```

#### Generate seeder for a specific table:

```bash
php artisan get:seeder users
```

#### Use a specific database connection:

```bash
php artisan get:seeder --connection=mysql
```

## Configuration

After publishing the config file, you can customize the following options in `config/db-craft.php`:

```php
return [
    // Database connection to use
    'connection' => env('DB_CRAFT_CONNECTION', null),
    
    // Path where migration files will be stored
    'migrations_path' => database_path('migrations'),
    
    // Path where seeder files will be stored
    'seeders_path' => database_path('seeders'),
    
    // Tables to exclude from generation
    'exclude_tables' => [
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'failed_jobs',
        'personal_access_tokens',
    ],
    
    // Number of records per chunk in seeders
    'seeder_chunk_size' => 100,
];
```

## Examples

### Reverse Engineering an Existing Database

If you have an existing database and want to create Laravel migrations:

```bash
# Connect to your existing database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_existing_db
DB_USERNAME=root
DB_PASSWORD=secret

# Generate all migrations
php artisan get:migration

# Generate all seeders
php artisan get:seeder
```

### Backing Up Table Data

Create seeders to backup your current data:

```bash
# Generate seeders for all tables
php artisan get:seeder

# Or for specific critical tables
php artisan get:seeder users
php artisan get:seeder products
php artisan get:seeder orders
```

### Working with Multiple Databases

```bash
# Generate migrations from production database
php artisan get:migration --connection=production

# Generate seeders from staging database
php artisan get:seeder --connection=staging
```

## Generated Files

### Migration Example

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### Seeder Example

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('users')->truncate();
        
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'created_at' => '2024-01-01 00:00:00',
                'updated_at' => '2024-01-01 00:00:00',
            ],
            // More records...
        ]);
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
```

## Supported Column Types

The package automatically maps database column types to Laravel migration types:

- Integer types (int, bigint, smallint, tinyint, mediumint)
- String types (varchar, char, text, mediumtext, longtext)
- Decimal types (decimal, float, double)
- Date/Time types (date, datetime, timestamp, time, year)
- JSON types (json, jsonb)
- Boolean types (boolean, bool)
- Binary types (binary)
- And more...

## Tips & Best Practices

1. **Review Generated Files**: Always review the generated migrations before running them
2. **Test in Development**: Test the migrations in a development environment first
3. **Backup Your Data**: Always backup your database before running migrations
4. **Large Tables**: For tables with millions of records, consider generating seeders in smaller batches
5. **Foreign Keys**: The package attempts to detect foreign keys, but verify them manually
6. **Timestamps**: Laravel's `timestamps()` helper is used when detected

## Troubleshooting

### Foreign Key Detection Issues

If foreign keys are not detected properly:
- Ensure your database user has proper permissions
- Check that foreign keys are properly defined in the database
- Some database configurations may not expose foreign key information

### Large Dataset Memory Issues

For very large tables:
- Adjust `seeder_chunk_size` in the config file
- Consider generating seeders for specific tables only
- Use database-specific dump tools for very large datasets

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- Inspired by [robinncode/db_craft](https://github.com/robinNcode/db_craft) for CodeIgniter 4
- Built with ❤️ for the Laravel community

## Support

If you find this package helpful, please consider:
- Starring the repository
- Sharing it with others
- Contributing improvements

For issues and feature requests, please use the [GitHub issue tracker](https://github.com/robinncode/laravel-db-craft/issues).