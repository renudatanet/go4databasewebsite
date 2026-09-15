<?php

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=new_go4database',
        'root',
        ''
    );

    echo "DATABASE CONNECTION SUCCESS";
    echo "<br>Database: " . $pdo->query("SELECT DATABASE()")->fetchColumn();
    echo "<br>Port: " . $pdo->query("SELECT @@port")->fetchColumn();

} catch (PDOException $e) {
    echo "DATABASE CONNECTION FAILED<br>";
    echo $e->getMessage();
}