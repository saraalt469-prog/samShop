<?php
$dsn = "pgsql:host=pgsqlDevelop;dbname=learn";
$username = 'postgres';
$password = 'root';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS feedback(
        id SERIAL PRIMARY KEY, 
        user_name VARCHAR(100),
        message TEXT
    )");


} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
