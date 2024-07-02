<?php
require_once '../config.php';
echo '<pre>';
use Illuminate\Database\Capsule\Manager as Capsule;
use Faker\Factory as Faker;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => $db_file,
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$faker = Faker::create();

$clients = [];
$tags = [];

Capsule::table('users')->insert([
    'name' => 'root',
    'password'=>password_hash('root', PASSWORD_DEFAULT)
]);

for ($i = 0; $i < 20; $i++) {
    $clients[] = Capsule::table('clients')->insertGetId([
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'second_name' => $faker->lastName,
        'phone' => $faker->phoneNumber,
        'company_name' => $faker->company,
        'comment' => $faker->realText,
    ]);
}

for ($i = 0; $i < 10; $i++) {
    $tags[] = Capsule::table('tags')->insertGetId([
        'name' => $faker->word,
    ]);
}

// Присваивание случайных тегов
for ($j = 0; $j < 30; $j++) {
    Capsule::table('client_tag')->insertOrIgnore([
        'client_id' => array_rand($clients),
        'tag_id' => array_rand($tags)
    ]);
}
echo '</pre>';
die("Таблицы наполнены тестовыми данными!");