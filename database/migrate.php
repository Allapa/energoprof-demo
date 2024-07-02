<?php
require_once __DIR__.'../config.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => $db_file,
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    Capsule::schema()->create('users', function ($table) {
        $table->increments('id');
        $table->string('name');
        $table->string('password');
        $table->timestamps();
    });

    Capsule::schema()->create('clients', function ($table) {
        $table->increments('id');
        $table->string('first_name');
        $table->string('last_name');
        $table->string('second_name');
        $table->string('phone', 20);
        $table->string('company_name')->nullable();
        $table->string('comment')->nullable();
        $table->timestamps();
    });

    Capsule::schema()->create('tags', function ($table) {
        $table->increments('id');
        $table->string('name');
        $table->timestamps();
    });

    Capsule::schema()->create('client_tag', function ($table) {
        $table->integer('client_id')->unsigned();
        $table->integer('tag_id')->unsigned();
        $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        $table->primary(['client_id', 'tag_id']);
    });
    die("Таблицы успешно созданы!");
} catch (PDOException $e) {
    die("Ошибка при создании таблиц: " . $e->getMessage());
}
