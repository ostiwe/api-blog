<?php

use Blog\Model\PostModel;
use Blog\Model\UserModel;

require_once __DIR__ . '/vendor/autoload.php';


$dirs = scandir(__DIR__);

if (!in_array('.env', $dirs)) {
	$config = file_get_contents(__DIR__ . '/.env.example');
	$key = uuid_create(CRYPT_MD5);
	$config = str_replace('APP_KEY=', "APP_KEY=$key", $config);
	file_put_contents(__DIR__ . '/.env', $config);


	Dotenv\Dotenv::createImmutable(__DIR__)->load();
	require_once __DIR__ . '/libs/db_config.php';

	R::nuke();

	$allPermissionsList = (new ReflectionClass(UserModel::class))->getConstants();
	$allPermissionsMask = 0;
	foreach ($allPermissionsList as $item) {
		$allPermissionsMask |= $item;
	}
	$admin = (new UserModel())
		->setFirstName('Admin')
		->setLastName('Site')
		->setSex(0)
		->setMask($allPermissionsMask)
		->setAge(21)
		->setEmail('admin@blog.com')
		->setUsername('admin')
		->setPassword('admin')
		->create();

	$post = (new PostModel())
		->setViews(0)
		->setTitle("Welcome!")
		->setText('This is the first post on your blog!')
		->setAuthor($admin)
		->create();

	R::getWriter()->addUniqueIndex('user', ['uid']);
	R::getWriter()->addUniqueIndex('user', ['username']);
	R::getWriter()->addUniqueIndex('user', ['email']);

	R::getWriter()->addUniqueIndex('post', ['uid']);

	echo "So you blog already done!\n\nYou may login on you blog used this login/password:\n admin/admin\n";
}
