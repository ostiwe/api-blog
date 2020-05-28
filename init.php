<?php

require_once __DIR__ . '/vendor/autoload.php';

$dirs = scandir(__DIR__);

if (!in_array('.env', $dirs)) {
	copy(__DIR__ . '/.env.example', __DIR__ . '/.env');
	$config = file_get_contents(__DIR__ . '/.env');
	$key = uuid_create(CRYPT_MD5);
	$config = str_replace('APP_KEY=', "APP_KEY=$key", $config);
	file_put_contents(__DIR__ . '/.env', $config);

}
