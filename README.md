
# Migration to Model

Migration to Model is a Laravel package that converts your database migrations into Eloquent models. It generates fillable properties, table names, and relationships based on your migration files.

## Installation

You can install the package via composer:

```bash
composer require noouh/migration-to-model
```

## Usage

To convert a specific migration file into a model, you can use the following artisan command:

```bash
php artisan migrations:convert {migration}
```

Replace `{migration}` with the name of your migration file (without the timestamp). For example, if your migration file is `2024_07_23_000000_create_users_table.php`, you would use:

```bash
php artisan migrations:convert create_users_table
```

To convert all migration files into models, simply run:

```bash
php artisan migrations:convert
```

This command will generate model files in the `app/Models` directory with the appropriate fillable properties, table names, and relationship methods based on detected foreign keys.

## Example

Suppose you have the following migration file:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->foreignId('profile_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

Running the command:

```bash
php artisan migrations:convert create_users_table
```

Will generate the following model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'profile_id'];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }
}
```

## License

The Migration to Model package is open-sourced software licensed under the [MIT license](LICENSE).
