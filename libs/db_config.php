<?php
include __DIR__ . '/rb.php';
$host = getenv("DB_HOST");
$port = getenv('DB_PORT');
$db_name = getenv("DB_NAME");
$db_userName = getenv("DB_USER");
$db_password = getenv("DB_PASS");
R::setup("mysql:host={$host};dbname={$db_name}",
    $db_userName, $db_password);


if (!R::testConnection()) {
    error_log("Unable to connect to database");

    header('Content-Type:Application/json');
    print_r(json_encode(['status' => 'server_error',
        'message' => 'Server error, retry the request later.']));

    exit();
}
