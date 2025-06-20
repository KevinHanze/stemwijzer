<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Database\PdoConnection;
use App\Mapper\UserMapper;

$db = new PdoConnection('sqlite:' . __DIR__ . '/../database.db');

$userMapper = new UserMapper($db);

$db->execute("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password  TEXT NOT NULL,
        roles     TEXT NOT NULL
    )
");

$userMapper->insert(new App\Model\User(null, 'kevin', password_hash('test', PASSWORD_DEFAULT), ['user']));
$userMapper->insert(new App\Model\User(null, 'ralf',  password_hash('test', PASSWORD_DEFAULT), ['admin']));

$user   = $userMapper->get(1);
$admins = $userMapper->select(new Framework\Database\Query(['roles' => '["admin"]']));

print_r($user);
print_r($admins);
