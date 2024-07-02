<?php
require_once __DIR__ . '/vendor/autoload.php';
const PACKAGE = '/energoprof-demo';
$db_file = __DIR__ . '/db.sqlite';
try {
    $db = new PDO("sqlite:$db_file");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}